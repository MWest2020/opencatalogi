<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ThemaController
 *
 * Controller for handling thema-related operations in the OpenCatalogi app.
 * @category Controller
 * @package opencatalogi
 * @author Ruben van der Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */
class ThemesController extends Controller
{
    /**
     * ThemaController constructor.
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
     * Retrieve a list of themas.
     *
     * @return JSONResponse JSON response containing the list of themas and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): JSONResponse
    {
        // Fetch themas using the ObjectService
        return $this->objectService->index('theme');
    }

    /**
     * Retrieve a specific thema by its ID.
     *
     * @param string $id The ID of the thema to retrieve
     * @return JSONResponse JSON response containing the requested thema
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function show(string $id): JSONResponse
    {
        // Fetch a specific thema using the ObjectService
        return $this->objectService->show($id, 'theme');
    }
} 