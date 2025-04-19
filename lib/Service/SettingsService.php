<?php

namespace OCA\OpenCatalogi\Service;

use OCP\IAppConfig;
use OCP\IRequest;
use OCP\App\IAppManager;
use Psr\Container\ContainerInterface;
use OCP\AppFramework\Http\JSONResponse;
use OC_App;

/**
 * Service for handling settings-related operations.
 *
 * Provides functionality for retrieving, saving, and loading settings,
 * as well as managing configuration for different object types.
 * 
 * @category Service
 * @package opencatalogi
 * @author Conduction Team
 * @copyright 2023 Conduction
 * @license EUPL-1.2
 * @version 1.0.0
 * @link https://github.com/OpenCatalogi/opencatalogi
 */
class SettingsService
{
    /** @var string $appName The name of the app */
    private string $appName;

    /** @var string $openRegisterAppId The ID of the OpenRegister app */
    private const OPENREGISTER_APP_ID = 'openregister';

    /** @var string $minOpenRegisterVersion The minimum required version of OpenRegister */
    private const MIN_OPENREGISTER_VERSION = '0.1.7';

    /**
     * SettingsService constructor.
     *
     * @param IAppConfig $config App configuration interface
     * @param IRequest $request Request interface
     * @param ContainerInterface $container Container for dependency injection
     * @param IAppManager $appManager App manager interface
     */
    public function __construct(
        private readonly IAppConfig $config,
        private readonly IRequest $request,
        private readonly ContainerInterface $container,
        private readonly IAppManager $appManager,
    ) {
        $this->appName = 'opencatalogi';
    }

    /**
     * Checks if OpenRegister is installed and meets version requirements
     *
     * @param string|null $minVersion Minimum required version (e.g. '1.0.0')
     * @return bool True if OpenRegister is installed and meets version requirements
     */
    public function isOpenRegisterInstalled(?string $minVersion = self::MIN_OPENREGISTER_VERSION): bool
    {
        if (!$this->appManager->isInstalled(self::OPENREGISTER_APP_ID)) {
            return false;
        }

        if ($minVersion === null) {
            return true;
        }

        $currentVersion = $this->appManager->getAppVersion(self::OPENREGISTER_APP_ID);
        return version_compare($currentVersion, $minVersion, '>=');
    }

    /**
     * Checks if OpenRegister is enabled
     *
     * @return bool True if OpenRegister is enabled
     */
    public function isOpenRegisterEnabled(): bool
    {
        return $this->appManager->isEnabled(self::OPENREGISTER_APP_ID);
    }

    /**
     * Attempts to install or update OpenRegister
     *
     * @param string|null $minVersion Minimum required version
     * @return bool True if installation/update was successful
     * @throws \RuntimeException If installation/update fails
     */
    public function installOrUpdateOpenRegister(?string $minVersion = self::MIN_OPENREGISTER_VERSION): bool
    {
        try {
            if (!$this->isOpenRegisterInstalled($minVersion)) {
                // First try to download the app from the app store
                if (!OC_App::downloadApp(self::OPENREGISTER_APP_ID)) {
                    throw new \RuntimeException('Failed to download OpenRegister from the app store');
                }
                
                // Then install the downloaded app
                if (!OC_App::installApp(self::OPENREGISTER_APP_ID)) {
                    throw new \RuntimeException('Failed to install OpenRegister');
                }

                // Enable the app after installation
                if (!OC_App::enable(self::OPENREGISTER_APP_ID)) {
                    throw new \RuntimeException('Failed to enable OpenRegister');
                }
            } elseif ($minVersion !== null) {
                // Check if update is needed
                $currentVersion = $this->appManager->getAppVersion(self::OPENREGISTER_APP_ID);
                if (version_compare($currentVersion, $minVersion, '<')) {
                    // First try to download the update from the app store
                    if (!OC_App::downloadApp(self::OPENREGISTER_APP_ID)) {
                        throw new \RuntimeException('Failed to download OpenRegister update from the app store');
                    }
                    
                    // Then update the app
                    if (!OC_App::updateApp(self::OPENREGISTER_APP_ID)) {
                        throw new \RuntimeException('Failed to update OpenRegister');
                    }
                }

                // Ensure the app is enabled after update
                if (!$this->isOpenRegisterEnabled()) {
                    if (!OC_App::enable(self::OPENREGISTER_APP_ID)) {
                        throw new \RuntimeException('Failed to enable OpenRegister after update');
                    }
                }
            }
            return true;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to install/update OpenRegister: ' . $e->getMessage());
        }
    }

    /**
     * Attempts to auto-configure registers and schemas
     *
     * @return array The updated configuration
     */
    public function autoConfigure(): array
    {
        try {
            $objectService = $this->getObjectService();
            $registers = $objectService->getRegisters();
            
            if (empty($registers)) {
                return [];
            }

            $configuration = [];
            foreach ($this->getSettings()['objectTypes'] as $type) {
                // Try to find a register with a matching name
                $matchingRegister = null;
                foreach ($registers as $register) {
                    if (stripos($register['title'], $type) !== false) {
                        $matchingRegister = $register;
                        break;
                    }
                }

                if ($matchingRegister) {
                    $configuration["{$type}_register"] = $matchingRegister['id'];
                    
                    // Try to find a matching schema
                    if (!empty($matchingRegister['schemas'])) {
                        foreach ($matchingRegister['schemas'] as $schema) {
                            if (stripos($schema['title'], $type) !== false) {
                                $configuration["{$type}_schema"] = $schema['id'];
                                break;
                            }
                        }
                    }
                }
            }

            return $configuration;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to auto-configure: ' . $e->getMessage());
        }
    }

    /**
     * Initializes the app with all required components
     *
     * @param string|null $minOpenRegisterVersion Minimum required OpenRegister version
     * @return array The initialization results
     */
    public function initialize(?string $minOpenRegisterVersion = self::MIN_OPENREGISTER_VERSION): array
    {
        $results = [
            'openRegister' => false,
            'autoConfigured' => false,
            'settingsLoaded' => false,
            'errors' => []
        ];

        try {
            // Check and install/update OpenRegister
            if (!$this->isOpenRegisterInstalled($minOpenRegisterVersion)) {
                $this->installOrUpdateOpenRegister($minOpenRegisterVersion);
            }
            $results['openRegister'] = true;

            // Auto-configure registers and schemas
            $configuration = $this->autoConfigure();
            if (!empty($configuration)) {
                $this->updateSettings($configuration);
                $results['autoConfigured'] = true;
            }

            // Load settings from file
            $this->loadSettings();
            $results['settingsLoaded'] = true;

        } catch (\Exception $e) {
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Attempts to retrieve the OpenRegister service from the container.
     *
     * @return mixed|null The OpenRegister service if available, null otherwise.
     * @throws \RuntimeException If the service is not available
     */
    public function getObjectService(): ?\OCA\OpenRegister\Service\ObjectService
    {
        if (in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
            return $this->container->get('OCA\OpenRegister\Service\ObjectService');
        }

        throw new \RuntimeException('OpenRegister service is not available.');
    }

    /**
     * Attempts to retrieve the Configuration service from the container.
     *
     * @return mixed|null The Configuration service if available, null otherwise.
     * @throws \RuntimeException If the service is not available
     */
    public function getConfigurationService(): ?\OCA\OpenRegister\Service\ConfigurationService
    {
        if (in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
            return $this->container->get('OCA\OpenRegister\Service\ConfigurationService');
        }

        throw new \RuntimeException('Configuration service is not available.');
    }

    /**
     * Retrieve the current settings.
     *
     * @return array The current settings configuration
     */
    public function getSettings(): array
    {
        // Initialize the data array
        $data = [];
        $data['objectTypes'] = ['catalog', 'listing', 'organization', 'theme', 'page', 'menu', 'glossary'];
        $data['openRegisters'] = false;
        $data['availableRegisters'] = [];

        // Check if the OpenRegister service is available
        try {
            $openRegisters = $this->getObjectService();
            if ($openRegisters !== null) {
                $data['openRegisters'] = true;
                $data['availableRegisters'] = $openRegisters->getRegisters();
            }
        } catch (\RuntimeException $e) {
            // Service not available, continue with default values
        }

        // Build defaults array dynamically based on object types
        $defaults = [];
        foreach ($data['objectTypes'] as $type) {
            // Always use openregister as source
            $defaults["{$type}_source"] = 'openregister';
            $defaults["{$type}_schema"] = '';
            $defaults["{$type}_register"] = '';
        }

        // Get the current values for the object types from the configuration
        try {
            foreach ($defaults as $key => $defaultValue) {
                $data['configuration'][$key] = $this->config->getValueString($this->appName, $key, $defaultValue);
            }
            return $data;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to retrieve settings: ' . $e->getMessage());
        }
    }

    /**
     * Update the settings configuration.
     *
     * @param array $data The settings data to update
     * @return array The updated settings configuration
     */
    public function updateSettings(array $data): array
    {
        try {
            // Update each setting in the configuration
            foreach ($data as $key => $value) {
                $this->config->setValueString($this->appName, $key, $value);
                // Retrieve the updated value to confirm the change
                $data[$key] = $this->config->getValueString($this->appName, $key);
            }
            return $data;
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to update settings: ' . $e->getMessage());
        }
    }

    /**
     * Load settings from the publication_register.json file.
     *
     * @return array The loaded settings configuration
     */
    public function loadSettings(): array
    {
        // Read the settings from the publication_register.json file
        $settingsFilePath = __DIR__ . '/../Settings/publication_register.json';
        $settings = [];

        try {
            // Check if the file exists
            if (!file_exists($settingsFilePath)) {
                throw new \Exception('Settings file not found.');
            }

            // Get the contents of the file
            $jsonContent = file_get_contents($settingsFilePath);

            // Decode the JSON content into an associative array
            $settings = json_decode($jsonContent, true);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error decoding JSON: ' . json_last_error_msg());
            }

            $includeObjects = false;

            // Get the configuration service and import the settings
            $configurationService = $this->getConfigurationService();
            return $configurationService->importFromJson($settings, $includeObjects);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to load settings: ' . $e->getMessage());
        }
    }
} 