<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\PublicationService;
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
 * @author Ruben van der Linde
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
     * @param PublicationService $publicationService The publication service
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly PublicationService $publicationService
    ) {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of publications based on all available catalogs.
     *
     * @param string|int|null $catalogId Optional ID of a specific catalog to filter by
     * @return JSONResponse JSON response containing the list of publications and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(string|int|null $catalogId = null): JSONResponse
    {
        return $this->publicationService->index($catalogId);
    }

    /**
     * Retrieve a specific publication by its ID.
     *
     * @param string|int|null $catalogId Optional ID of the catalog to filter by
     * @param string $publicationId The ID of the publication to retrieve
     * @return JSONResponse JSON response containing the requested publication
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int|null $catalogId, string $publicationId): JSONResponse
    {
        return $this->publicationService->show($catalogId, $publicationId);
    }
}
