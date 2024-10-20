<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\PublicationTypeMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

/**
 * Class PublicationTypesController
 *
 * Controller for handling publication type-related operations in the OpenCatalogi app.
 */
class PublicationTypesController extends Controller
{
    /**
     * PublicationTypesController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param IAppConfig $config The app configuration
     * @param PublicationTypeMapper $publicationTypeMapper The publication type mapper
     * @param ObjectService $objectService The object service
     * @param DirectoryService $directoryService The directory service
     */
    public function __construct(
        $appName,
        IRequest $request,
        private readonly IAppConfig $config,
        private readonly PublicationTypeMapper $publicationTypeMapper,
        private readonly ObjectService $objectService,
        private readonly DirectoryService $directoryService
    )
    {
        parent::__construct($appName, $request);
    }

    /**
     * Render the main page for publication types.
     *
     * @return TemplateResponse The rendered template response
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function page(): TemplateResponse
    {
        // Return a template response for the publication type index page
        return new TemplateResponse(
            $this->appName,
            'publicationTypeIndex',
            []
        );
    }

    /**
     * Retrieve a list of publication types based on provided filters and parameters.
     *
     * @return JSONResponse JSON response containing the list of publication types and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch publication type objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('publicationType', $requestParams);

        // Return JSON response with the fetched data
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific publication type by its ID.
     *
     * @param string|int $id The ID of the publication type to retrieve
     * @return JSONResponse JSON response containing the requested publication type
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id): JSONResponse
    {
        // Fetch the publication type object by its ID
        $object = $this->objectService->getObject('publicationType', $id);

        // Return the publication type as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new publication type.
     *
     * @return JSONResponse The response containing the created publication type object
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

        // Save the new publication type object
        $object = $this->objectService->saveObject('publicationType', $data);

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing publication type.
     *
     * @param string|int $id The ID of the publication type to update
     * @return JSONResponse The response containing the updated publication type object
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

        // Save the updated publication type object
        $object = $this->objectService->saveObject('publicationType', $data);

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a publication type.
     *
     * @param string|int $id The ID of the publication type to delete
     * @return JSONResponse The response indicating the result of the deletion
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id): JSONResponse
    {
        // Delete the publication type object
        $result = $this->objectService->deleteObject('publicationType', $id);

        // Return the result as a JSON response
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }
    
	/**
	 * Synchronize or delete a publication type based on listing status
	 *
	 * @PublicPage
	 * @NoCSRFRequired
	 * @return JSONResponse The JSON response containing the result of the operation
	 */
	public function synchronise(): JSONResponse
	{
		// Get the source and listed parameters from the request
		$source = $this->request->getParam('source');
		$listed = $this->request->getParam('listed', false);

		// Check if the source parameter is provided
		if (empty($source)) {
			return new JSONResponse(['error' => 'source parameter is required'], 400);
		}

		try {
			if ($listed) {
				// If listed is true, synchronize the publication type
				$syncedPublicationType = $this->directoryService->syncPublicationType($source);
				return new JSONResponse($syncedPublicationType);
			} else {
				// If listed is false, attempt to delete the publication type
				// @todo: we cant get a single object by parameters yet but we can use the find method and grab the first array result   
				// Check if a publication type with the same name already exists
                $publicationTypes = $this->objectService->getObjects(
                    objectType: 'publicationType',
                    filters: [
                        ['source' => $source]
                    ]
                );
				
				// Check the number of publication types found
				if (count($publicationTypes) === 1) {
					$publicationType = $publicationTypes[0];
				} elseif (count($publicationTypes) > 1) {
					// If multiple publication types are found, return an error
					return new JSONResponse(['error' => 'Multiple publication types found for the given source'], 409);
				} else {
					// If no publication types are found, return an error
					return new JSONResponse(['error' => 'Publication type not found'], 404);
				}

				// If a publication type is found, attempt to delete it
				if ($publicationType) {
					$result = $this->objectService->deleteObject('publicationType', $publicationType['id']);
					return new JSONResponse(['success' => $result], $result === true ? 200 : 404);
				}
				
				// If no publication type is found (this should not be reached due to earlier check)
				return new JSONResponse(['message' => 'Publication type not found'], 404);
			}
		} catch (\Exception $e) {
			// If an exception occurs, return an error response
			return new JSONResponse(['error' => 'An error occurred: ' . $e->getMessage()], 500);
		}
	}
}
