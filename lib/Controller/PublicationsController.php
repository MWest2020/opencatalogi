<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class PublicationsController
 *
 * Controller for handling publication-related operations in the OpenCatalogi app.
 * @category Controller
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */
class PublicationsController extends Controller
{
    /**
     * PublicationsController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param ObjectService $objectService The object service
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly ObjectService $objectService
    ) {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of catalogs based on provided filters and parameters.
     *
     * @param string|int $id The ID of the catalog to use as a filter
     * @return JSONResponse JSON response containing the list of catalogs and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(string|int $id): JSONResponse
    {
        // Get the catalog from the object service
        $catalog = $this->objectService->find($id);

        // If catalog not found, return empty result
        if (!$catalog) {
            return new JSONResponse([
                'results' => [],
                'total' => 0
            ]);
        }

        // Get all objects using the catalog's registers and schemas as filters
        $objects = $this->objectService->findAll([
            'registers' => $catalog['registers'] ?? [],
            'schemas' => $catalog['schemas'] ?? []
        ]);

        return new JSONResponse([
            'results' => $objects,
            'total' => count($objects)
        ]);
    }
}
