<?php

namespace OCA\OpenCatalogi\Controller;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\FileService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use OCP\IUserSession;
use Symfony\Component\Uid\Uuid;

class AttachmentsController extends Controller
{

    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
		private readonly AttachmentMapper $attachmentMapper,
		private readonly FileService $fileService,
		private readonly IUserSession $userSession,
		private readonly ObjectService $objectService
	)
    {
        parent::__construct($appName, $request);
    }


    /**
     * Retrieve a list of attachments based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the list of attachments and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
        // Retrieve all request parameters
        $filters = $this->request->getParams();

        // Extract specific parameters
        $limit = $this->request->getParam('limit', null);
        $offset = $this->request->getParam('offset', null);
        $order = $this->request->getParam('order', []);

        // Remove unnecessary parameters from filters
        unset($filters['_route']); // TODO: Investigate why this is here and if it's needed
        unset($filters['_extend'], $filters['_limit'], $filters['_offset'], $filters['_order']);

        // Fetch attachment objects based on filters and order
        $objects = $this->objectService->getObjects('attachment', null, null, $filters, null, null, $order);

        // Prepare response data
        $data = [
            'results' => $objects,
            'total' => count($objects)
        ];

        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific attachment by its ID.
     *
     * @param string|int $id The ID of the attachment to retrieve
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the requested attachment
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Fetch the attachment object by its ID
        $object = $this->objectService->getObject('attachment', $id);

        // Return the attachment as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new attachment.
     *
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the created attachment object.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(ObjectService $objectService): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();
        
        // Remove the 'id' field if it exists, as we're creating a new object
        unset($data['id']);

        // Save the new attachment object
        $object = $this->objectService->saveObject('attachment', $data);
        
        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing attachment.
     *
     * @param string|int $id The ID of the attachment to update.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the updated attachment object.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();
        
        // Ensure the ID in the data matches the ID in the URL
        $data['id'] = $id;
        
        // Save the updated attachment object
        $object = $this->objectService->saveObject('attachment', $data);
        
        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete an attachment.
     *
     * @param string|int $id The ID of the attachment to delete.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the attachment object
        $result = $this->objectService->deleteObject('attachment', $id);
        
        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }


	/**
	 * Gets info about the uploaded file from the request body, looks specifically for the field '_file'.
	 * If there is no file or there is an error loading it this will return an error response.
	 *
	 * @return JSONResponse|array An error response or an array containing the info about the uploaded file.
	 */
	private function checkUploadedFile(): JSONResponse|array
	{
		$uploadedFile = $this->request->getUploadedFile(key: '_file');

		if (empty($uploadedFile) === true) {
			return new JSONResponse(data: ['error' => 'Please upload a file using key "_file" or give a "downloadUrl"'], statusCode: 400);
		}

		// Check for upload errors.
		if ($uploadedFile['error'] !== UPLOAD_ERR_OK) {
			return new JSONResponse(data: ['error' => 'File upload error: '.$uploadedFile['error']], statusCode: 400);
		}

		return $uploadedFile;
	}

	/**
	 * Validates if the URL fields are actual valid urls (or null).
	 *
	 * @param array $data The form-data fields and their values (/request body).
	 *
	 * @return JSONResponse|array An error response if there are validation errors or an array containing all request body params.
	 */
	private function checkRequestBody(array $data): JSONResponse|array
	{
		$errorMsg = [];
		if (empty($data['accessUrl']) === false && filter_var(value: $data['accessUrl'], filter: FILTER_VALIDATE_URL) === false) {
			$errorMsg[] = "accessUrl is not a valid url";
		}

		if (empty($data['downloadUrl']) === false && filter_var(value: $data['downloadUrl'], filter: FILTER_VALIDATE_URL) === false) {
			$errorMsg[] = "downloadUrl is not a valid url";
		}

		if (empty($errorMsg) === false) {
			return new JSONResponse(data: ['validation_errors' => $errorMsg], statusCode: 400);
		}

		return $data;
	}

	/**
	 * If it does not already exist creates a folder for the publication the new Attachment belongs to in NextCloud,
	 * so that the uploaded file(s) for that publication can be saved there. After that saves the uploaded file in that folder.
	 * If the file is created without error this will return the full path to the file from the root/user folder.
	 *
	 * @param array $uploadedFile Information about the uploaded file from the request body.
	 *
	 * @return JSONResponse|string An error response if creating the file in NextCloud failed or a string path to the created file.
	 * @throws Exception In case creating a folder or new file fails.
	 */
	private function handleFile(array $uploadedFile): JSONResponse|string
	{
		// Create the Publicaties folder, the Publication specific folder and the Bijlagen folder in that.
		$this->fileService->createFolder(folderPath: 'Publicaties');
		$publicationFolder = $this->fileService->getPublicationFolderName(
			publicationId: $this->request->getHeader('Publication-Id'),
			publicationTitle: $this->request->getHeader('Publication-Title')
		);
		$this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder");
		$this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder/Bijlagen");

		// Save the uploaded file.
		$filePath = "Publicaties/$publicationFolder/Bijlagen/" . $uploadedFile['name']; // Add a file version to the file name?
		$created = $this->fileService->uploadFile(
			content: file_get_contents(filename: $uploadedFile['tmp_name']),
			filePath: $filePath
		);

		if ($created === false) {
			return new JSONResponse(data: ['error' => "Failed to upload file. This file: $filePath might already exist"], statusCode: 400);
		}

		return $filePath;
	}


	/**
	 * Adds information about the uploaded file to the appropriate Attachment fields. Inclusive share link.
	 *
	 * @param array $data The form-data fields and their values (/request body) that we are going to update before posting the Attachment.
	 * @param array $uploadedFile Information about the uploaded file from the request body.
	 * @param string $filePath The full file path to where the file is stored in NextCloud.
	 *
	 * @return array The updated $data array
	 * @throws Exception In case creating the share(link) fails.
	 */
	private function AddFileInfoToData(array $data, array $uploadedFile, string $filePath): array
	{
		// Update Attachment data.
		$currentUser = $this->userSession->getUser();
		$userId = $currentUser ? $currentUser->getUID() : 'Guest';
		$data['reference'] = "$userId/$filePath";
		$data['type'] = $uploadedFile['type'];
		$data['size'] = $uploadedFile['size'];
		$explodedName = explode(separator: '.', string: $uploadedFile['name']);
		$data['title'] = $explodedName[0];
		$data['extension'] = end(array: $explodedName);

		// Create ShareLink.
		$shareLink = $this->fileService->createShareLink(path: $filePath);
		if (empty($data['accessUrl']) === true) {
			$data['accessUrl'] = $shareLink;
		}
		if (empty($data['downloadUrl']) === true) {
			$data['downloadUrl'] =  "$shareLink/download";
		}

		return $data;
	}
}
