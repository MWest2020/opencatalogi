<?php

namespace OCA\OpenCatalogi\Controller;

use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\ThemeMapper;
use OCA\OpenCatalogi\Service\ObjectService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

/**
 * Class ThemesController
 *
 * This controller handles CRUD operations for themes in the OpenCatalogi app.
 */
class ThemesController extends Controller
{
    /**
     * Constructor for ThemesController
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param ThemeMapper $themeMapper The theme mapper for database operations
     * @param IAppConfig $config The app configuration
     * @param ObjectService $objectService The service for handling object operations
     */
    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly ThemeMapper $themeMapper,
		private readonly IAppConfig $config,
		private readonly ObjectService $objectService
	)
    {
        parent::__construct($appName, $request);
    }

    /**
     * Retrieve a list of themes based on provided filters and parameters.
     *
     * @return JSONResponse JSON response containing the list of themes and total count
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): JSONResponse
    {
        // Retrieve all request parameters
        $requestParams = $this->request->getParams();

        // Fetch theme objects based on filters and order
        $data = $this->objectService->getResultArrayForRequest('theme', $requestParams);

        // Return JSON response
        return new JSONResponse($data);
    }

    /**
     * Retrieve a specific theme by its ID.
     *
     * @param string|int $id The ID of the theme to retrieve
     * @return JSONResponse JSON response containing the requested theme
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(string|int $id): JSONResponse
    {
        // Fetch the theme object by its ID
        $object = $this->objectService->getObject('theme', $id);

        // Return the theme as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Create a new theme.
     *
     * @return JSONResponse The response containing the created theme object.
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

        // Save the new theme object
        $object = $this->objectService->saveObject('theme', $data);

        // Return the created object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Update an existing theme.
     *
     * @param string|int $id The ID of the theme to update.
     * @return JSONResponse The response containing the updated theme object.
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

        // Save the updated theme object
        $object = $this->objectService->saveObject('theme', $data);

        // Return the updated object as a JSON response
        return new JSONResponse($object);
    }

    /**
     * Delete a theme.
     *
     * @param string|int $id The ID of the theme to delete.
     * @return JSONResponse The response indicating the result of the deletion.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(string|int $id): JSONResponse
    {
        // Delete the theme object
        $result = $this->objectService->deleteObject('theme', $id);

        // Return the result as a JSON response
		return new JSONResponse(['success' => $result], $result === true ? '200' : '404');
    }
}
