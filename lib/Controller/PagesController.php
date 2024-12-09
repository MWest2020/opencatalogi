<?php

namespace OCA\OpenCatalogi\Controller;

use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\PageMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

/**
 * Class PagesController
 *
 * This controller handles CRUD operations for pages in the OpenCatalogi app.
 */
class PagesController extends Controller
{
    /**
     * Constructor for PagesController
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param PageMapper $pageMapper The page mapper for database operations
     * @param IAppConfig $config The app configuration
     * @param ObjectService $objectService The service for handling object operations
     */
    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly PageMapper $pageMapper,
		private readonly IAppConfig $config,
		private readonly ObjectService $objectService
	)
    {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of pages based on provided filters and parameters.
     *
     * @return JSONResponse JSON response containing the list of pages and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch page objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('page', $requestParams);

        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific page by its ID.
     *
     * @param string|int $id The ID of the page to retrieve
     * @return JSONResponse JSON response containing the requested page
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id): JSONResponse
    {
        // Fetch the page object by its ID
        $object = $this->objectService->getObject('page', $id);

        // Return the page as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new page.
     *
     * @return JSONResponse The response containing the created page object.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();

        // Remove the 'id' field if it exists, as we're creating a new object
        unset($data['id']);

        // Save the new page object
        $object = $this->objectService->saveObject('page', $data);

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing page.
     *
     * @param string|int $id The ID of the page to update.
     * @return JSONResponse The response containing the updated page object.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string|int $id): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();

        // Ensure the ID in the data matches the ID in the URL
        $data['id'] = $id;

        // Save the updated page object
        $object = $this->objectService->saveObject('page', $data);

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a page.
     *
     * @param string|int $id The ID of the page to delete.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id): JSONResponse
    {
        // Delete the page object
        $result = $this->objectService->deleteObject('page', $id);

        // Return the result as a JSON response
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }
}
