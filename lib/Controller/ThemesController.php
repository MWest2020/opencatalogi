<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\App\IAppManager;
use Psr\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ThemesController
 *
 * Controller for handling theme-related operations in the OpenCatalogi app.
 *
 * @category  Controller
 * @package   opencatalogi
 * @author    Ruben van der Linde
 * @copyright 2024
 * @license   AGPL-3.0-or-later
 * @version   1.0.0
 * @link      https://github.com/opencatalogi/opencatalogi
 */
class ThemesController extends Controller
{


    /**
     * ThemesController constructor.
     *
     * @param string             $appName       The name of the app
     * @param IRequest           $request       The request object
     * @param ContainerInterface $container     Server container for dependency injection
     * @param IAppManager        $appManager    App manager for checking installed apps
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly ContainerInterface $container,
        private readonly IAppManager $appManager
    ) {
        parent::__construct($appName, $request);

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


    /**
     * Get all themes.
     *
     * @return JSONResponse The JSON response containing the list of themes
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): JSONResponse
    {
        $config = [
            'filters' => ['schema' => 'theme']
        ];

        $result = $this->getObjectService()->findAll($config);
        
        $data = [
            'results' => array_map(function ($object) {
                return $object instanceof \OCP\AppFramework\Db\Entity ? $object->jsonSerialize() : $object;
            }, $result ?? []),
            'total' => count($result ?? [])
        ];

        return new JSONResponse($data);

    }//end index()


    /**
     * Get a specific theme by its ID.
     *
     * @param string|int $id The ID of the theme to retrieve
     *
     * @return JSONResponse The JSON response containing the theme details
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function show(string|int $id): JSONResponse
    {
        $theme = $this->getObjectService()->find($id);
        
        $data = $theme instanceof \OCP\AppFramework\Db\Entity ? $theme->jsonSerialize() : $theme;
        
        return new JSONResponse($data);

    }//end show()


}//end class
