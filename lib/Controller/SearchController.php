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
use OCP\IUserManager;
use OCP\IUserSession;
use OCP\App\IAppManager;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;


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
     * @param string             $appName            The name of the app
     * @param IRequest           $request            The request object
     * @param PublicationMapper  $publicationMapper  The publication mapper
     * @param IAppConfig         $config             The app configuration
     * @param ContainerInterface $container          Server container for dependency injection
     * @param IAppManager        $appManager         App manager for checking installed apps
     * @param string             $corsMethods        Allowed CORS methods
     * @param string             $corsAllowedHeaders Allowed CORS headers
     * @param int                $corsMaxAge         CORS max age
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly PublicationMapper $publicationMapper,
        private readonly IAppConfig $config,
        private readonly IUserManager $userManager,
        private readonly IUserSession $userSession,
        private readonly ContainerInterface $container,
        private readonly IAppManager $appManager,
        $corsMethods='PUT, POST, GET, DELETE, PATCH',
        $corsAllowedHeaders='Authorization, Content-Type, Accept',
        $corsMaxAge=1728000
    ) {
        parent::__construct($appName, $request);
        $this->corsMethods        = $corsMethods;
        $this->corsAllowedHeaders = $corsAllowedHeaders;
        $this->corsMaxAge         = $corsMaxAge;

    }//end __construct()


    /**
     * Attempts to retrieve the OpenRegister ObjectService from the container.
     *
     * @return \OCA\OpenRegister\Service\ObjectService|null The OpenRegister ObjectService if available, null otherwise.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     */
    private function getObjectService(): ?\OCA\OpenRegister\Service\ObjectService
    {
        if (in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
            return $this->container->get('OCA\OpenRegister\Service\ObjectService');
        }

        throw new \RuntimeException('OpenRegister service is not available.');

    }//end getObjectService()


    /*
     * Implements a preflighted CORS response for OPTIONS requests.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     * @since           7.0.0
     *
     * @return Response The CORS response
     */
    #[NoCSRFRequired]
    #[PublicPage]


    public function preflightedCors(): Response
    {
        // Determine the origin
        $origin = isset($this->request->server['HTTP_ORIGIN']) ? $this->request->server['HTTP_ORIGIN'] : '*';

        // Create and configure the response
        $response = new Response();
        $response->addHeader('Access-Control-Allow-Origin', $origin);
        $response->addHeader('Access-Control-Allow-Methods', $this->corsMethods);
        $response->addHeader('Access-Control-Max-Age', (string) $this->corsMaxAge);
        $response->addHeader('Access-Control-Allow-Headers', $this->corsAllowedHeaders);
        $response->addHeader('Access-Control-Allow-Credentials', 'false');

        return $response;

    }//end preflightedCors()


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

        // Build config for findAll to get publications
        $config = [
            'filters' => ['schema' => 'publication']
        ];
        
        // Add any additional filters from request params
        if (isset($requestParams['filters'])) {
            $config['filters'] = array_merge($config['filters'], $requestParams['filters']);
        }
        
        // Add pagination and other params
        if (isset($requestParams['limit'])) {
            $config['limit'] = (int) $requestParams['limit'];
        }
        if (isset($requestParams['offset'])) {
            $config['offset'] = (int) $requestParams['offset'];
        }

        // Get publication objects
        $result = $this->getObjectService()->findAll($config);
        
        // Convert objects to arrays and filter to only include published publications
        $publications = array_map(function ($object) {
            return $object instanceof \OCP\AppFramework\Db\Entity ? $object->jsonSerialize() : $object;
        }, $result ?? []);
        
        $filteredObjects = array_filter(
            $publications,
            function ($object) {
                  return isset($object['status']) && $object['status'] === 'Published' && isset($object['published']) && $object['published'] !== null;
              }
        );

        // Prepare the response data
        $data = [
            'results' => array_values($filteredObjects),
// Reset array keys
            'total'   => count($filteredObjects),
        ];

        return new JSONResponse($data);

    }//end index()


}//end class
