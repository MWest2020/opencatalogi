<?php

namespace OCA\OpenCatalogi\Controller;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Mpdf\MpdfException;
use Mpdf\Output\Destination;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\opencatalogi\lib\Db\Publication;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Service\FileService;
use OCA\OpenCatalogi\Service\DownloadService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\SearchService;
use OCA\OpenCatalogi\Service\ValidationService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\IAppConfig;
use OCP\IRequest;
use Symfony\Component\Uid\Uuid;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PublicationsController
 *
 * Controller for handling publication-related operations in the OpenCatalogi app.
 */
class PublicationsController extends Controller
{
    /**
     * PublicationsController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param PublicationMapper $publicationMapper The publication mapper
     * @param AttachmentMapper $attachmentMapper The attachment mapper
     * @param IAppConfig $config The app configuration
     * @param FileService $fileService The file service
     * @param DownloadService $downloadService The download service
     * @param ObjectService $objectService The object service
     */
    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly PublicationMapper $publicationMapper,
		private readonly AttachmentMapper $attachmentMapper,
		private readonly IAppConfig $config,
		private readonly FileService $fileService,
		private readonly DownloadService $downloadService,
		private ObjectService $objectService
	)
    {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of publications based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the list of publications and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch publication objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('publication', $requestParams);

        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific publication by its ID.
     *
     * @param string|int $id The ID of the publication to retrieve
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the requested publication
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Fetch the publication object by its ID
        $object = $this->objectService->getObject('publication', $id);

        // Return the publication as a JSON response
        return new JSONResponse($object);
    }

	/**
	 * Return all attachments for given publication.
	 *
	 * @param string|int $id The id of the publication
	 * @param ObjectService $objectService The Object Service
	 * @param array|null $publication A publication array (unused in this method)
	 *
	 * @return JSONResponse The Response containing attachments
	 * @throws GuzzleException
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function attachments(string|int $id, ObjectService $objectService, ?array $publication = null): JSONResponse
	{
		// Get request parameters
		$filters = $this->request->getParams();
		$filter['publication'] = $id;
		$limit = $this->request->getParam('limit', null);
		$offset = $this->request->getParam('offset', null);
		$order = $this->request->getParam('order', []);

		// Remove unnecessary parameters
		unset($filters['_route']); //@todo why is this here?
		unset($filters['_extend']);
		unset($filters['_limit']);
		unset($filters['_offset']);
		unset($filters['_order']);

		// Fetch attachment objects
		$objects = $this->objectService->getObjects('attachment', null, null, $filters, null, null, $order);

		// Prepare response data
		$data = [
			'results' => $objects,
			'total' => count($objects)
		];

		return new JSONResponse($data);
	}

	/**
	 * Create/updates a file containing all metadata of a publication to NextCloud files, finds/creates a share link and returns it.
	 *
	 * @param string $filename The (tmp) filename of the file to store in NextCloud files
	 * @param array $publication The publication data used to find/create the publication specific folder in NextCloud files
	 *
	 * @return string|JSONResponse A share link url or an error JSONResponse
	 * @throws Exception When a function reading or writing to NextCloud files goes wrong
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function saveFileToNextCloud(string $filename, array $publication): string|JSONResponse
	{
		// Create the Publicaties folder and the Publication specific folder
		$this->fileService->createFolder(folderPath: 'Publicaties');
		$publicationFolder = $this->fileService->getPublicationFolderName(
			publicationId: $publication['id'],
			publicationTitle: $publication['title']
		);
		$this->fileService->createFolder(folderPath: "Publicaties/$publicationFolder");

		// Save the file to NextCloud
		$filePath = "Publicaties/$publicationFolder/$filename";
		$created = $this->fileService->updateFile(
			content: file_get_contents(filename: $filename),
			filePath: $filePath,
			createNew: true
		);

		// Check if file creation was successful
		if ($created === false) {
			return new JSONResponse(data: ['error' => "Failed to upload this file: $filePath to NextCloud"], statusCode: 500);
		}

		// Create or find ShareLink
		$share = $this->fileService->findShare(path: $filePath);
		if ($share !== null) {
			$shareLink = $this->fileService->getShareLink($share);
		} else {
			$shareLink = $this->fileService->createShareLink(path: $filePath);
		}

		return $shareLink;
	}

	/**
	 * Download a publication in either PDF or ZIP format.
	 *
	 * This method handles the download request for a publication, supporting both PDF and ZIP formats.
	 * The format is determined by the 'Accept' header in the request.
	 *
	 * @param string|int $id The ID of the publication to download
	 * @param ObjectService $objectService The service to handle object operations
	 * @return JSONResponse The response containing either the file download or an error message
	 *
	 * @throws LoaderError|MpdfException|RuntimeError|SyntaxError
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function download(string|int $id, ObjectService $objectService): JSONResponse
	{
		// Determine the requested format based on the 'Accept' header
		return match ($this->request->getHeader('Accept')) {
			// If PDF is requested, create and return a PDF file
			'application/pdf' => $this->downloadService->createPublicationFile(objectService: $objectService, id: $id),
			// If ZIP is requested, create and return a ZIP file
			'application/zip' => $this->downloadService->createPublicationZip(objectService: $objectService, id: $id),
			// If an unsupported format is requested, return an error response
			default => new JSONResponse(
				data: ['error' => 'Unsupported Accept header, please use [application/pdf] or [application/zip]'],
				statusCode: 400
			),
		};
	}

    /**
     * Create a new publication.
     *
     * @param ObjectService $objectService The service to handle object operations
     * @return JSONResponse The response containing the created publication object
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

        // Save the new publication object
        $object = $this->objectService->saveObject('publication', $data);

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing publication.
     *
     * @param string|int $id The ID of the publication to update
     * @param ObjectService $objectService The service to handle object operations
     * @return JSONResponse The response containing the updated publication object
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

        // Save the updated publication object
        $object = $this->objectService->saveObject('publication', $data);

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a publication.
     *
     * @param string|int $id The ID of the publication to delete
     * @param ObjectService $objectService The service to handle object operations
     * @return JSONResponse The response indicating the result of the deletion
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the publication object
        $result = $this->objectService->deleteObject('publication', $id);

        // Return the result as a JSON response
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }
}
