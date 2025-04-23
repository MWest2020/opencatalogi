<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class GlossaryController
 *
 * Controller for handling glossary-related operations in the OpenCatalogi app.
 *
 * @category  Controller
 * @package   opencatalogi
 * @author    Ruben van der Linde
 * @copyright 2024
 * @license   AGPL-3.0-or-later
 * @version   1.0.0
 * @link      https://github.com/opencatalogi/opencatalogi
 */
class GlossaryController extends Controller
{


    /**
     * GlossaryController constructor.
     *
     * @param string        $appName       The name of the app
     * @param IRequest      $request       The request object
     * @param ObjectService $objectService The object service
     */
    public function __construct(
        string $appName,
        IRequest $request,
        private readonly ObjectService $objectService
    ) {
        parent::__construct($appName, $request);

    }//end __construct()


    /**
     * Retrieve a list of glossary terms.
     *
     * @return JSONResponse JSON response containing the list of glossary terms and total count
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function index(): JSONResponse
    {
        // Fetch glossary terms using the ObjectService
        return $this->objectService->index('glossary');

    }//end index()


    /**
     * Retrieve a specific glossary term by its ID.
     *
     * @param  string $id The ID of the glossary term to retrieve
     * @return JSONResponse JSON response containing the requested glossary term
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     * @PublicPage
     */
    public function show(string $id): JSONResponse
    {
        // Fetch a specific glossary term using the ObjectService
        return $this->objectService->show($id, 'glossary');

    }//end show()


}//end class
