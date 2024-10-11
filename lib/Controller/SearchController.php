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
		ObjectService $objectService,
		private readonly PublicationMapper $publicationMapper,
        private readonly IAppConfig $config,
		$corsMethods = 'PUT, POST, GET, DELETE, PATCH',
		$corsAllowedHeaders = 'Authorization, Content-Type, Accept',
		$corsMaxAge = 1728000
	) {
		parent::__construct($appName, $request);
		$this->objectService = $objectService;
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
	public function publications(): JSONResponse
	{
		return new JSONResponse([]);
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
		return new JSONResponse([]);
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
		return new JSONResponse([]);
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
		return new JSONResponse([]);
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
		return new JSONResponse([]);
	}

}
