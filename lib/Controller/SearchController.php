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

class SearchController extends Controller
{

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
	 * This method implements a preflighted cors response for you that you can
	 * link to for the options request
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @PublicPage
	 * @since 7.0.0
	 */
	#[NoCSRFRequired]
	#[PublicPage]
	public function preflightedCors() {
		if (isset($this->request->server['HTTP_ORIGIN'])) {
			$origin = $this->request->server['HTTP_ORIGIN'];
		} else {
			$origin = '*';
		}

		$response = new Response();
		$response->addHeader('Access-Control-Allow-Origin', $origin);
		$response->addHeader('Access-Control-Allow-Methods', $this->corsMethods);
		$response->addHeader('Access-Control-Max-Age', (string)$this->corsMaxAge);
		$response->addHeader('Access-Control-Allow-Headers', $this->corsAllowedHeaders);
		$response->addHeader('Access-Control-Allow-Credentials', 'false');
		return $response;
	}	

	/**
	 * Return all attachments for given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 *
	 * @return JSONResponse The Response.
	 * @throws GuzzleException
	 */
	public function index(): JSONResponse
	{
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

		$objects = $this->objectService->getResultArrayForRequest('publication', $requestParams);

		// Filter objects to only include published publications
		$filteredObjects = array_filter($objects['results'], function($object) {
			return isset($object['status']) && $object['status'] === 'Published' && isset($object['published']) && $object['published'] !== null;
		});

		$data = [
			'results' => array_values($filteredObjects), // Reset array keys
			'total' => count($filteredObjects)
		];
		return new JSONResponse($data);
	}

	/**
	 * Return all attachments for given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 *
	 * @return JSONResponse The Response.
	 * @throws GuzzleException
	 */
	public function publications(): JSONResponse
	{
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

		$objects = $this->objectService->getResultArrayForRequest('publication', $requestParams);

		// Filter objects to only include published publications
		$filteredObjects = array_filter($objects['results'], function($object) {
			return isset($object['status']) && $object['status'] === 'Published' && isset($object['published']) && $object['published'] !== null;
		});

		$data = [
			'results' => array_values($filteredObjects), // Reset array keys
			'total' => count($filteredObjects)
		];
		return new JSONResponse($data);
	}
	
	/**
	 * Return all attachments for given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $publicationId The id.
	 *
	 * @return JSONResponse The Response.
	 * @throws GuzzleException
	 */
	public function publication(string|int $publicationId): JSONResponse
	{
		$object = $this->objectService->getObject('publication', $publicationId);
		return new JSONResponse($object);
	}

	/**
	 * Return all attachments for given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $publicationId The id.
	 *
	 * @return JSONResponse The Response.
	 * @throws GuzzleException
	 */
	public function attachments(string|int $publicationId): JSONResponse
	{
		$objects = $this->objectService->getObjects('attachment');

		$data = [
			'results' => $objects,
			'total' => count($objects)
		];
		return new JSONResponse($objects);
	}

	/**
	 * Return all attachments for given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $publicationId The id.
	 *
	 * @return JSONResponse The Response.
	 * @throws GuzzleException
	 */
	public function attachment(string|int $attachmentId): JSONResponse
	{
		$object = $this->objectService->getObject('attachment', $attachmentId);
		return new JSONResponse($object);
	}


	/**
	 * Return all attachments for given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 *
	 * @return JSONResponse The Response.
	 * @throws GuzzleException
	 */
	public function themes(): JSONResponse
	{
		$objects = $this->objectService->getObjects('attachment');

		$data = [
			'results' => $objects,
			'total' => count($objects)
		];
		return new JSONResponse($objects);
	}
	
	/**
	 * Return all attachments for given publication.
	 *
	 * @CORS
	 * @PublicPage
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string|int $themeId The id.
	 *
	 * @return JSONResponse The Response.
	 * @throws GuzzleException
	 */
	public function theme(string|int $themeId): JSONResponse
	{
		$object = $this->objectService->getObject('theme', $themeId);
		return new JSONResponse($object);
	}

}
