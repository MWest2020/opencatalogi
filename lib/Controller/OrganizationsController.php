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
use OCP\IURLGenerator;
/**
 * Class OrganizationsController
 *
 * Controller for handling organization-related operations in the OpenCatalogi app.
 */
class OrganizationsController extends Controller
{
    /**
     * OrganizationsController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param IAppConfig $config The app configuration
     * @param OrganizationMapper $organizationMapper The organization mapper
     * @param ObjectService $objectService The object service           
     * @param IURLGenerator $urlGenerator The URL generator
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly OrganizationMapper $organizationMapper,
        private readonly ObjectService $objectService,
        private readonly IURLGenerator $urlGenerator
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * Render the main page for organizations.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return TemplateResponse The rendered template response
     */
    public function page(): TemplateResponse
    {
        // Return a template response for the organization index page
        return new TemplateResponse($this->appName, 'OrganizationIndex', []);
    }

    /**
     * Retrieve a list of organizations based on provided filters and parameters.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return JSONResponse JSON response containing the list of organizations and total count
     */
    public function index(): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch organization objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('organization', $requestParams);

        // Return JSON response with the fetched data
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific organization by its ID.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @param string|int $id The ID of the organization to retrieve
     * @return JSONResponse JSON response containing the requested organization
     */
    public function show(string|int $id): JSONResponse
    {
        // Fetch the organization object by its ID
        $object = $this->objectService->getObject('organization', $id);

        // Return the organization as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new organization.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @return JSONResponse The response containing the created organization object
     */
    public function create(): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();

        // Remove the 'id' field if it exists, as we're creating a new object
        unset($data['id']);

        // Save the new organization object
        $object = $this->objectService->saveObject('organization', $data);

        // If object is a class change it to array
        if (is_object($object)) {
            $object = $object->jsonSerialize();
        }

        // If we do not have an uri, we need to generate one
        if (isset($object['uri']) === false) {
            $object['uri'] = $this->urlGenerator->linkToRoute('openCatalogi.organizations.show', ['id' => $object['id']]);
            $object = $this->objectService->saveObject('organization', $object);
        }

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing organization.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @param string|int $id The ID of the organization to update
     * @return JSONResponse The response containing the updated organization object
     */
    public function update(string|int $id): JSONResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();

        // Ensure the ID in the data matches the ID in the URL
        $data['id'] = $id;

        // If we do not have an uri, we need to generate one
        if (isset($data['uri']) === false) {
            $data['uri'] = $this->urlGenerator->linkToRoute('openCatalogi.organizations.show', ['id' => $data['id']]);
            $data = $this->objectService->saveObject('organization', $data);
        }

        // Save the updated organization object
        $object = $this->objectService->saveObject('organization', $data);

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete an organization.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     *
     * @param string|int $id The ID of the organization to delete
     * @return JSONResponse The response indicating the result of the deletion
     */
    public function destroy(string|int $id): JSONResponse
    {
        // Delete the organization object
        $result = $this->objectService->deleteObject('organization', $id);

        // Return the result as a JSON response
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }
}
