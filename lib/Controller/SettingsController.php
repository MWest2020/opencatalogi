<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\IAppConfig;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use Psr\Container\ContainerInterface;
use OCP\App\IAppManager;
use OCA\OpenCatalogi\Service\SettingsService;

/**
 * @class SettingsController
 * @category Controller
 * @package OpenCatalogi
 * @subpackage Controller
 * @author Conduction Team
 * @copyright 2023 Conduction
 * @license EUPL-1.2
 * @version 1.0.0
 * @link https://github.com/OpenCatalogi/opencatalogi
 * 
 * Controller for handling settings-related operations in the OpenCatalogi.
 */
class SettingsController extends Controller
{
	/**
	 */
	private $objectService;

	/**
	 * SettingsController constructor.
	 *
	 * @param string $appName The name of the app
	 * @param IRequest $request The request object
	 * @param IAppConfig $config The app configuration
	 * @param ContainerInterface $container The container
	 * @param IAppManager $appManager The app manager
	 * @param SettingsService $settingsService The settings service
	 */
	public function __construct(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
		private readonly ContainerInterface $container,
		private readonly IAppManager $appManager,
		private readonly SettingsService $settingsService,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * Attempts to retrieve the OpenRegister service from the container.
	 *
	 * @return mixed|null The OpenRegister service if available, null otherwise.
	 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
	 */
	public function getObjectService(): ?\OCA\OpenRegister\Service\ObjectService
	{
		if (in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
			$this->objectService = $this->container->get('OCA\OpenRegister\Service\ObjectService');

			return $this->objectService;
		}

		throw new \RuntimeException('OpenRegister service is not available.');
	}

	/**
	 * Attempts to retrieve the Configuration service from the container.
	 *
	 * @return mixed|null The Configuration service if available, null otherwise.
	 * @throws ContainerExceptionInterface|NotFoundExceptionInterface
	 */
	public function getConfigurationService(): ?\OCA\OpenRegister\Service\ConfigurationService
	{
		// Check if the 'openregister' app is installed
		if (in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
			// Retrieve the ConfigurationService from the container
			$configurationService = $this->container->get('OCA\OpenRegister\Service\ConfigurationService');

			return $configurationService;
		}

		// Throw an exception if the service is not available
		throw new \RuntimeException('Configuration service is not available.');
	}

	/**
	 * Retrieve the current settings.
	 *
	 * @return JSONResponse JSON response containing the current settings
	 *
	 * @NoCSRFRequired
	 */
	public function index(): JSONResponse
	{
		try {
			$data = $this->settingsService->getSettings();
			return new JSONResponse($data);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], 500);
		}
	}

	/**
	 * Handle the post request to update settings.
	 *
	 * @return JSONResponse JSON response containing the updated settings
	 *
	 * @NoCSRFRequired
	 */
	public function create(): JSONResponse
	{
		try {
			$data = $this->request->getParams();
			$result = $this->settingsService->updateSettings($data);
			return new JSONResponse($result);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], 500);
		}
	}

	/**
	 * Load the settings from the publication_register.json file.
	 *
	 * @return JSONResponse JSON response containing the settings
	 *
	 * @NoCSRFRequired
	 */
	public function load(): JSONResponse
	{
		try {
			$result = $this->settingsService->loadSettings();
			return new JSONResponse($result);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], 500);
		}
	}
}
