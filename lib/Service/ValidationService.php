<?php

namespace OCA\OpenCatalogi\Service;

use OCA\OpenCatalogi\Db\CatalogMapper;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\IAppConfig;

/**
 * Class ValidationService
 *
 * This service handles validation of catalogs and publications.
 */
class ValidationService
{
	/**
	 * @var string The name of the application.
	 */
	private string $appName;

	/**
	 * @var array The current MongoDB Config.
	 */
	private array $mongodbConfig;

	/**
	 * ValidationService constructor.
	 *
	 * @param IAppConfig    $config		   The application config
	 * @param CatalogMapper $catalogMapper The catalog mapper.
	 * @param ObjectService $objectService The object service.
	 */
	public function __construct(
		private readonly IAppConfig    $config,
		private readonly CatalogMapper $catalogMapper,
		private readonly ObjectService $objectService,
	) {
		$this->appName = 'opencatalogi';

		// Initialize MongoDB configuration
		$this->mongodbConfig = [
			'base_uri' => $this->config->getValueString(app: $this->appName, key: 'mongodbLocation'),
			'headers' => ['api-key' => $this->config->getValueString(app: $this->appName, key: 'mongodbKey')],
			'mongodbCluster' => $this->config->getValueString(app: $this->appName, key:'mongodbCluster')
		];
	}

	/**
	 * Get the MongoDB configuration.
	 *
	 * @return array The mongodb config.
	 */
	public function getMongodbConfig(): array
	{
		return $this->mongodbConfig;
	}

	/**
	 * Fetches a catalog from either the local database or mongodb
	 *
	 * @param  string $id The id of the catalog to be fetched.
	 * @return array      The JSON Serialised catalog.
	 *
	 * @throws OCSNotFoundException If the catalog is not found.
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function getCatalog (string $id): array
	{
		// Check if MongoDB storage is enabled
		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') !== false
			&& $this->config->getValueString(app: $this->appName, key: 'mongoStorage') === '1'
		) {
			$filter = ['id' => $id, '_schema' => 'catalog'];

            try {
                return $this->objectService->findObject(filters: $filter, config: $this->getMongodbConfig());
            } catch (OCSNotFoundException $exception) {
			    throw new OCSNotFoundException(message: 'Catalog not found for id: ' . $id);
            }
		}

		// If MongoDB storage is not enabled, fetch from local database
		return $this->catalogMapper->find(id: $id)->jsonSerialize();
	}

	/**
	 * Validates a publication against the rules set for the publication.
	 *
	 * @param  array $publication The publication to be validated.
	 * @return array 			  The publication after it has been validated.
	 *
	 * @throws OCSBadRequestException Thrown if the object does not validate
	 * @throws OCSNotFoundException   Thrown if the catalog is not found
	 */
	public function validatePublication(array $publication): array
	{
        // Check for required fields
        $requiredFields = ['catalogi', 'publicationType'];
        foreach ($requiredFields as $field) {
            if (isset($publication[$field]) === false) {
                throw new OCSBadRequestException(message: $field . ' is required but not given.');
            }
        }

		$catalog  = $publication['catalogi'];
		$publicationType   = $publication['publicationType'];

        try {
		    $catalog = $this->getCatalog($catalog);
        } catch (OCSNotFoundException $exception) {
            throw new OCSNotFoundException(message: $exception->getMessage());
        }

		// Check if the given publicationType is present in the catalog
		if (in_array(needle: $publicationType, haystack: $catalog['publicationType']) === false) {
			throw new OCSBadRequestException(message: 'Given publicationType object not present in catalog');
		}

		return $publication;
	}

}
