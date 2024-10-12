<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\OrganizationMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class OrganizationsController extends Controller
{
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly OrganizationMapper $organizationMapper,
        private readonly ObjectService $objectService
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * This returns the template of the main app's page
     * It adds some data to the template (app version)
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return TemplateResponse
     */
    public function page(): TemplateResponse
    {
        return new TemplateResponse($this->appName, 'OrganizationIndex', []);
    }

    /**
     * Retrieve a list of organizations based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the list of organizations and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch organization objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('organization', $requestParams);
        
        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific organization by its ID.
     *
     * @param string|int $id The ID of the organization to retrieve
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the requested organization
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Fetch the organization object by its ID
        $object = $this->objectService->getObject('organization', $id);

        // Return the organization as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new organization.
     *
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the created organization object.
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

        // Save the new organization object
        $object = $this->objectService->saveObject('organization', $data);
        
        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing organization.
     *
     * @param string|int $id The ID of the organization to update.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the updated organization object.
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
        
        // Save the updated organization object
        $object = $this->objectService->saveObject('organization', $data);
        
        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete an organization.
     *
     * @param string|int $id The ID of the organization to delete.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the organization object
        $result = $this->objectService->deleteObject('organization', $id);
        
        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }
}
