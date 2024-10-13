<?php

namespace OCA\OpenCatalogi\Controller;

use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Service\ElasticSearchService;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Service\SearchService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\Response;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;
use OCA\OpenCatalogi\Service\ObjectService;

/**
 * Class SearchController
 *
 * Controller for handling search-related operations in the OpenCatalogi app.
 */
class SearchController extends Controller
{
    /**
     * SearchController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param ObjectService $objectService The object service
     * @param PublicationMapper $publicationMapper The publication mapper
     * @param IAppConfig $config The app configuration
     * @param string $corsMethods Allowed CORS methods
     * @param string $corsAllowedHeaders Allowed CORS headers
     * @param int $corsMaxAge CORS max age
     */
    public function __construct(
        $appName,
        IRequest $request,
		private ObjectService $objectService,
		private readonly PublicationMapper $publicationMapper,
        private readonly IAppConfig $config,
		$corsMethods = 'PUT, POST, GET, DELETE, PATCH',
		$corsAllowedHeaders = 'Authorization, Content-Type, Accept',
		$corsMaxAge = 1728000
	) {
		parent::__construct($appName, $request);
		$this->corsMethods = $corsMethods;
		$this->corsAllowedHeaders = $corsAllowedHeaders;
		$this->corsMaxAge = $corsMaxAge;
    }

	/**
	 * Implements a preflighted CORS response for OPTIONS requests.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 * @since 7.0.0
	 *
	 * @return Response The CORS response
	 */
	#[NoCSRFRequired]
	#[PublicPage]
	public function preflightedCors(): Response {
		// Determine the origin
		$origin = isset($this->request->server['HTTP_ORIGIN']) ? $this->request->server['HTTP_ORIGIN'] : '*';

		// Create and configure the response
		$response = new Response();
		$response->addHeader('Access-Control-Allow-Origin', $origin);
		$response->addHeader('Access-Control-Allow-Methods', $this->corsMethods);
		$response->addHeader('Access-Control-Max-Age', (string)$this->corsMaxAge);
		$response->addHeader('Access-Control-Allow-Headers', $this->corsAllowedHeaders);
		$response->addHeader('Access-Control-Allow-Credentials', 'false');
		
		return $response;
	}	

	/**
	 * Return all published publications.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse The Response containing published publications.
	 * @throws GuzzleException
	 */
	public function index(): JSONResponse
	{
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

		// Get publication objects based on request parameters
		$objects = $this->objectService->getResultArrayForRequest('publication', $requestParams);

		// Filter objects to only include published publications
		$filteredObjects = array_filter($objects['results'], function($object) {
			return isset($object['status']) && $object['status'] === 'Published' && isset($object['published']) && $object['published'] !== null;
		});

		// Prepare the response data
		$data = [
			'results' => array_values($filteredObjects), // Reset array keys
			'total' => count($filteredObjects)
		];
		
		return new JSONResponse($data);
	}

	/**
	 * Return all published publications.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse The Response containing published publications.
	 * @throws GuzzleException
	 */
	public function publications(): JSONResponse
	{
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

		// Get publication objects based on request parameters
		$objects = $this->objectService->getResultArrayForRequest('publication', $requestParams);

		// Filter objects to only include published publications
		$filteredObjects = array_filter($objects['results'], function($object) {
			return isset($object['status']) && $object['status'] === 'Published' && isset($object['published']) && $object['published'] !== null;
		});

		// Prepare the response data
		$data = [
			'results' => array_values($filteredObjects), // Reset array keys
			'total' => count($filteredObjects)
		];
		
		return new JSONResponse($data);
	}
	
	/**
	 * Return a specific publication by ID.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $publicationId The ID of the publication.
	 *
	 * @return JSONResponse The Response containing the requested publication.
	 * @throws GuzzleException
	 */
	public function publication(string|int $publicationId): JSONResponse
	{
		// Get the publication object by ID
		$object = $this->objectService->getObject('publication', $publicationId);
		return new JSONResponse($object);
	}

	/**
	 * Return all attachments for a given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $publicationId The ID of the publication.
	 *
	 * @return JSONResponse The Response containing the attachments.
	 * @throws GuzzleException
	 */
	public function attachments(string|int $publicationId): JSONResponse
	{
		// Get all attachment objects
		$objects = $this->objectService->getObjects('attachment');

		// Prepare the response data
		$data = [
			'results' => $objects,
			'total' => count($objects)
		];
		
		return new JSONResponse($objects);
	}

	/**
	 * Return a specific attachment by ID.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $attachmentId The ID of the attachment.
	 *
	 * @return JSONResponse The Response containing the requested attachment.
	 * @throws GuzzleException
	 */
	public function attachment(string|int $attachmentId): JSONResponse
	{
		// Get the attachment object by ID
		$object = $this->objectService->getObject('attachment', $attachmentId);
		return new JSONResponse($object);
	}

	/**
	 * Return all themes.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @return JSONResponse The Response containing all themes.
	 * @throws GuzzleException
	 */
	public function themes(): JSONResponse
	{
		// Get all attachment objects (Note: This might be a mistake, should probably be 'theme' instead of 'attachment')
		$objects = $this->objectService->getObjects('attachment');

		// Prepare the response data
		$data = [
			'results' => $objects,
			'total' => count($objects)
		];
		
		return new JSONResponse($objects);
	}
	
	/**
	 * Return a specific theme by ID.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $themeId The ID of the theme.
	 *
	 * @return JSONResponse The Response containing the requested theme.
	 * @throws GuzzleException
	 */
	public function theme(string|int $themeId): JSONResponse
	{
		// Get the theme object by ID
		$object = $this->objectService->getObject('theme', $themeId);
		return new JSONResponse($object);
	}

}
