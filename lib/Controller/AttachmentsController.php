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

/**
 * Class AttachmentsController
 *
 * This controller handles CRUD operations for attachments in the OpenCatalogi app.
 */
class AttachmentsController extends Controller
{
    /**
     * AttachmentsController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param IAppConfig $config The app configuration
     * @param AttachmentMapper $attachmentMapper The attachment mapper
     * @param FileService $fileService The file service
     * @param IUserSession $userSession The user session
     * @param ObjectService $objectService The object service
     */
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
        $requestParams = $this->request->getParams();

        // Fetch attachment objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('attachment', $requestParams);

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
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }

    /**
     * Gets info about the uploaded file from the request body, looks specifically for the field '_file'.
     * If there is no file or there is an error loading it this will return an error response.
     *
     * @return JSONResponse|array An error response or an array containing the info about the uploaded file.
     */
    private function checkUploadedFile(): JSONResponse|array
    {
        // Get the uploaded file from the request
        $uploadedFile = $this->request->getUploadedFile(key: '_file');

        // Check if a file was uploaded
        if (empty($uploadedFile) === true) {
            return new JSONResponse(data: ['error' => 'Please upload a file using key "_file" or give a "downloadUrl"'], statusCode: 400);
        }

        // Check for upload errors
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

        // Validate accessUrl if it exists
        if (empty($data['accessUrl']) === false && filter_var(value: $data['accessUrl'], filter: FILTER_VALIDATE_URL) === false) {
            $errorMsg[] = "accessUrl is not a valid url";
        }

        // Validate downloadUrl if it exists
        if (empty($data['downloadUrl']) === false && filter_var(value: $data['downloadUrl'], filter: FILTER_VALIDATE_URL) === false) {
            $errorMsg[] = "downloadUrl is not a valid url";
        }

        // Return error response if there are validation errors
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
        // Create the Publicaties folder
        $this->fileService->createFolder(folderPath: 'Publicaties');

        // Get the publication folder name
        $publicationFolder = $this->fileService->getPublicationFolderName(
            publicationId: $this->request->getHeader('Publication-Id'),
            publicationTitle: $this->request->getHeader('Publication-Title')
        );

        // Create the publication-specific folder and the Bijlagen folder within it
        $this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder");
        $this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder/Bijlagen");

        // Construct the file path
        $filePath = "Publicaties/$publicationFolder/Bijlagen/" . $uploadedFile['name']; // TODO: Consider adding a file version to the file name

        // Upload the file
        $created = $this->fileService->uploadFile(
            content: file_get_contents(filename: $uploadedFile['tmp_name']),
            filePath: $filePath
        );

        // Check if the file was created successfully
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
        // Get the current user
        $currentUser = $this->userSession->getUser();
        $userId = $currentUser ? $currentUser->getUID() : 'Guest';

        // Update Attachment data
        $data['reference'] = "$userId/$filePath";
        $data['type'] = $uploadedFile['type'];
        $data['size'] = $uploadedFile['size'];
        $explodedName = explode(separator: '.', string: $uploadedFile['name']);
        $data['title'] = $explodedName[0];
        $data['extension'] = end(array: $explodedName);

        // Create ShareLink
        $shareLink = $this->fileService->createShareLink(path: $filePath);

        // Set accessUrl if not already set
        if (empty($data['accessUrl']) === true) {
            $data['accessUrl'] = $shareLink;
        }

        // Set downloadUrl if not already set
        if (empty($data['downloadUrl']) === true) {
            $data['downloadUrl'] =  "$shareLink/download";
        }

        return $data;
    }
}
