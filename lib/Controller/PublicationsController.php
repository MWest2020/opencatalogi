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
 *
 * @category  Controller
 * @package   opencatalogi
 * @author    Ruben van der Linde
 * @copyright 2024
 * @license   AGPL-3.0-or-later
 * @version   1.0.0
 * @link      https://github.com/opencatalogi/opencatalogi
 */
class PublicationsController extends Controller
{


    /**
     * PublicationsController constructor.
     *
     * @param string             $appName            The name of the app
     * @param IRequest           $request            The request object
     * @param PublicationService $publicationService The publication service
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly PublicationService $publicationService
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
        return $this->publicationService->index();

    }//end index()


    /**
     * Retrieve a specific publication by its ID.
     *
     * @param  string|int|null $catalogId     Optional ID of the catalog to filter by
     * @param  string          $publicationId The ID of the publication to retrieve
     * @return JSONResponse JSON response containing the requested publication
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function show(string $id): JSONResponse
    {
        return $this->publicationService->show(id: $id);

    }//end show()


    /**
     * Retrieve attachments/files of a publication.
     *
     * @param  string $id Id of publication
     *
     * @return JSONResponse JSON response containing the requested attachments/files.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function attachments(string $id): JSONResponse
    {
        return $this->publicationService->attachments(id: $id);

    }//end show()


    /**
     * Retrieve attachments/files of a publication.
     *
     * @param  string $id Id of publication
     *
     * @return JSONResponse JSON response containing the requested attachments/files.
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function download(string $id): JSONResponse
    {
        return $this->publicationService->download(id: $id);

    }//end show()


    /**
     * Retrieves all objects that this publication references
     *
     * This method returns all objects that this publication uses/references. A -> B means that A (This publication) references B (Another object).
     *
     * @param string $id The ID of the publication to retrieve relations for
     * @return JSONResponse A JSON response containing the related objects
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function uses(string $id): JSONResponse
    {
        return $this->publicationService->uses(id: $id);
    }


    /**
     * Retrieves all objects that use this publication
     *
     * This method returns all objects that reference (use) this publication. B -> A means that B (Another object) references A (This publication).
     *
     * @param string $id The ID of the publication to retrieve uses for
     * @return JSONResponse A JSON response containing the referenced objects
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function used(string $id): JSONResponse
    {
        return $this->publicationService->used(id: $id);
    }

}//end class
