<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\ListingMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class ListingController extends Controller
{
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly ListingMapper $listingMapper,
        private readonly ObjectService $objectService,
        private readonly DirectoryService $directoryService
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of listings based on provided filters and parameters.
     *
     * @return JSONResponse JSON response containing the list of listings and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch listing objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('listing', $requestParams);
        
        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific listing by its ID.
     *
     * @param string|int $id The ID of the listing to retrieve
     * @return JSONResponse JSON response containing the requested listing
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id): JSONResponse
    {
        // Fetch the listing object by its ID
        $object = $this->objectService->getObject('listing', $id);

        // Return the listing as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new listing.
     *
     * @return JSONResponse The response containing the created listing object.
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

        // Save the new listing object
        $object = $this->objectService->saveObject('listing', $data);
        
        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing listing.
     *
     * @param string|int $id The ID of the listing to update.
     * @return JSONResponse The response containing the updated listing object.
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
        
        // Save the updated listing object
        $object = $this->objectService->saveObject('listing', $data);
        
        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a listing.
     *
     * @param string|int $id The ID of the listing to delete.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id): JSONResponse
    {
        // Delete the listing object
        $result = $this->objectService->deleteObject('listing', $id);
        
        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }

    /**
     * Synchronize a listing or all listings.
     *
     * @param string|int|null $id The ID of the listing to synchronize (optional).
     * @return JSONResponse The response indicating the result of the synchronization.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function synchronise(?string $id = null): JSONResponse
    {
        $result = $this->directoryService->synchronise($id);
        return new JSONResponse(['success' => $result]);
    }

    /**
     * Add a new listing from a URL.
     *
     * @return JSONResponse The response indicating the result of adding the listing.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function add(): JSONResponse
    {
        $url = $this->request->getParam('url');
        $result = $this->directoryService->add($url);
        return new JSONResponse(['success' => $result]);
    }
}
