<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\CatalogiService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class CatalogiController
 * Controller for handling catalog-related operations in the OpenCatalogi app.
 *
 * @category  Controller
 * @package   opencatalogi
 * @author    Ruben Linde
 * @copyright 2024
 * @license   AGPL-3.0-or-later
 * @version   1.0.0
 * @link      https://github.com/opencatalogi/opencatalogi
 */
class CatalogiController extends Controller
{


    /**
     * CatalogiController constructor.
     *
     * @param string             $appName            The name of the app
     * @param IRequest           $request            The request object
     * @param PublicationService $publicationService The publication service
     * @param ObjectService      $objectService      The object service
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly CatalogiService $catalogiService,
        private readonly ObjectService $objectService
    ) {
        parent::__construct($appName, $request);

    }//end __construct()


    /**
     * Retrieve a list of publications based on all available catalogs.
     *
     * @param  string|int|null $catalogId Optional ID of a specific catalog to filter by
     * @return JSONResponse JSON response containing the list of publications and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): JSONResponse
    {
        
        return $this->objectService->index(objectName: 'catalog');

    }//end index()

    /**
     * Retrieve a list of catalogs based on provided filters and parameters.
     *
     * @param  string|int $id The ID of the catalog to use as a filter
     * @return JSONResponse JSON response containing the list of catalogs and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string | int $id): JSONResponse
    {
        // Get all objects using the catalog's registers and schemas as filters
        $objects = $this->catalogiService->index($id);

        return $objects;

    }//end show()


}//end class
