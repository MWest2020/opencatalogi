<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\PublicationTypeMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class PublicationTypesController extends Controller
{
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly PublicationTypeMapper $publicationTypeMapper,
        private readonly ObjectService $objectService
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function page(): TemplateResponse
    {
        return new TemplateResponse(
            $this->appName,
            'publicationTypeIndex',
            []
        );
    }

    /**
     * Retrieve a list of publication types based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the list of publication types and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch publication type objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('publicationType', $requestParams);
        
        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific publication type by its ID.
     *
     * @param string|int $id The ID of the publication type to retrieve
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the requested publication type
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Fetch the publication type object by its ID
        $object = $this->objectService->getObject('publicationType', $id);

        // Return the publication type as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new publication type.
     *
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the created publication type object.
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

        // Save the new publication type object
        $object = $this->objectService->saveObject('publicationType', $data);
        
        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing publication type.
     *
     * @param string|int $id The ID of the publication type to update.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the updated publication type object.
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
        
        // Save the updated publication type object
        $object = $this->objectService->saveObject('publicationType', $data);
        
        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a publication type.
     *
     * @param string|int $id The ID of the publication type to delete.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the publication type object
        $result = $this->objectService->deleteObject('publicationType', $id);
        
        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }
}
