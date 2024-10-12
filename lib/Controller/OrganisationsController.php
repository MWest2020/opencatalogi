<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\OrganisationMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class OrganisationsController extends Controller
{
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly OrganisationMapper $organisationMapper,
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
        return new TemplateResponse($this->appName, 'OrganisationIndex', []);
    }

    /**
     * Retrieve a list of organisations based on provided filters and parameters.
     *
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the list of organisations and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(ObjectService $objectService): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch organisation objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('organisation', $requestParams);
        
        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific organisation by its ID.
     *
     * @param string|int $id The ID of the organisation to retrieve
     * @param ObjectService $objectService Service to handle object operations
     * @return JSONResponse JSON response containing the requested organisation
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Fetch the organisation object by its ID
        $object = $this->objectService->getObject('organisation', $id);

        // Return the organisation as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new organisation.
     *
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the created organisation object.
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

        // Save the new organisation object
        $object = $this->objectService->saveObject('organisation', $data);
        
        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing organisation.
     *
     * @param string|int $id The ID of the organisation to update.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response containing the updated organisation object.
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
        
        // Save the updated organisation object
        $object = $this->objectService->saveObject('organisation', $data);
        
        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete an organisation.
     *
     * @param string|int $id The ID of the organisation to delete.
     * @param ObjectService $objectService The service to handle object operations.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id, ObjectService $objectService): JSONResponse
    {
        // Delete the organisation object
        $result = $this->objectService->deleteObject('organisation', $id);
        
        // Return the result as a JSON response
        return new JSONResponse(['success' => $result]);
    }
}
