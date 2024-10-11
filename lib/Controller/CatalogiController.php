<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\SearchService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class CatalogiController extends Controller
{
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
		private readonly CatalogMapper $catalogMapper
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of catalogs based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
     * @param SearchService $searchService Service to handle search operations (unused in this method)
     * @return JSONResponse JSON response containing the list of catalogs and total count
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
        unset($filters['_route']); // TODO: Investigate why this is here and if it's needed
        unset($filters['_extend'], $filters['_limit'], $filters['_offset'], $filters['_order']);

        // Fetch catalog objects based on filters and order
        $objects = $this->objectService->getObjects('catalog', null, null, $filters, null, null, $order);

        // Prepare response data
        $data = [
            'results' => $objects,
            'total' => count($objects)
        ];

        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific catalog by its ID.
     *
     * @param string|int $id The ID of the catalog to retrieve
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the requested catalog
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Fetch the catalog object by its ID
        $object = $this->objectService->getObject('catalog', $id);

        // Return the catalog as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new catalog.
     *
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the created catalog object.
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

        // Save the new catalog object
        $object = $this->objectService->saveObject('catalog', $data);
        
        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing catalog.
     *
     * @param string|int $id The ID of the catalog to update.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the updated catalog object.
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
        
        // Save the updated catalog object
        $object = $this->objectService->saveObject('catalog', $data);
        
        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a catalog.
     *
     * @param string|int $id The ID of the catalog to delete.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the catalog object
        $result = $this->objectService->deleteObject('catalog', $id);
        
        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }
}
