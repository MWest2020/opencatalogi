<?php

namespace OCA\OpenCatalogi\Controller;

use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\ThemeMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\SearchService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class ThemesController extends Controller
{
    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly ThemeMapper $themeMapper,
		private readonly IAppConfig $config,
	)
    {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of themes based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
     * @param SearchService $searchService Service to handle search operations
     * @return JSONResponse JSON response containing the list of themes and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService, SearchService $searchService): JSONResponse
    {
        // Retrieve all request parameters
        $filters = $this->request->getParams();

        // Extract specific parameters
        $limit = $this->request->getParam('limit', null);
        $offset = $this->request->getParam('offset', null);
        $order = $this->request->getParam('order', []);

        // Remove unnecessary parameters from filters
        unset($filters['_route']);
        unset($filters['_extend'], $filters['_limit'], $filters['_offset'], $filters['_order']);

        // Fetch theme objects based on filters and order
        $objects = $this->objectService->getObjects('theme', null, null, $filters, null, null, $order);

        // Prepare response data
        $data = [
            'results' => $objects,
            'total' => count($objects)
        ];

        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific theme by its ID.
     *
     * @param string|int $id The ID of the theme to retrieve
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the requested theme
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Fetch the theme object by its ID
        $object = $this->objectService->getObject('theme', $id);

        // Return the theme as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new theme.
     *
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the created theme object.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(ObjectService $objectService): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();
        
        // Remove the 'id' field if it exists, as we're creating a new object
        unset($data['id']);

        // Save the new theme object
        $object = $this->objectService->saveObject('theme', $data);
        
        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing theme.
     *
     * @param string|int $id The ID of the theme to update.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the updated theme object.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();
        
        // Ensure the ID in the data matches the ID in the URL
        $data['id'] = $id;
        
        // Save the updated theme object
        $object = $this->objectService->saveObject('theme', $data);
        
        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a theme.
     *
     * @param string|int $id The ID of the theme to delete.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the theme object
        $result = $this->objectService->deleteObject('theme', $id);
        
        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }
}
