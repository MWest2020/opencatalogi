<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\NotFoundResponse;
use OCP\IRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PageController
 *
 * Controller for handling page-related operations in the OpenCatalogi app.
 * @category Controller
 * @package opencatalogi
 * @author Ruben van der Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */
class PagesController extends Controller
{
    /**
     * PageController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param ObjectService $objectService The object service
     */
    public function __construct(
        string $appName,
        IRequest $request,
        private readonly ObjectService $objectService
    ) {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of pages.
     *
     * @return JSONResponse JSON response containing the list of pages and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): JSONResponse
    {
        // Fetch pages using the ObjectService
        return $this->objectService->index('page');
    }

    /**
     * Retrieve a specific page by its slug.
     *
     * @param string $slug The slug of the page to retrieve
     * @return JSONResponse|NotFoundResponse JSON response containing the requested page or NotFoundResponse if not found
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function show(string $slug): JSONResponse|NotFoundResponse
    {
        // Set up the filter to search by slug
		$config = $this->objectService->getConfig('page');
        $config['filters']['slug'] = $slug;
        $config['limit'] = 1;
        
        // Get alle the pages
		$objects = $this->objectService->getObjectService()->findAll($config);

        // Check if we found a page
        if (empty($objects)) {
            return new NotFoundResponse();
        }        

        // Return the first (and only) result
        return new JSONResponse($objects[0]);
    }
} 