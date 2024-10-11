<?php

namespace OCA\OpenCatalogi\Service;

use Adbar\Dot;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Uid\Uuid;
use Psr\Container\ContainerInterface;
use OCP\IAppConfig;
// Lets grab the mappers
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCA\OpenCatalogi\Db\MetaDataMapper;
use OCA\OpenCatalogi\Db\OrganisationMapper;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Db\ThemeMapper;

class ObjectService
{
	private string $appName;

	/**
	 * Constructor for ObjectService.
	 *
	 * @param AttachmentMapper $attachmentMapper Mapper for attachments
	 * @param CatalogMapper $catalogMapper Mapper for catalogs
	 * @param ListingMapper $listingMapper Mapper for listings
	 * @param MetaDataMapper $metadataMapper Mapper for metadata
	 * @param OrganisationMapper $organisationMapper Mapper for organisations
	 * @param PublicationMapper $publicationMapper Mapper for publications
	 * @param ThemeMapper $themeMapper Mapper for themes
	 * @param ContainerInterface $container Container for dependency injection
	 * @param IAppConfig $config App configuration
	 */
	public function __construct(
		private AttachmentMapper $attachmentMapper,
		private CatalogMapper $catalogMapper,
		private ListingMapper $listingMapper,
		private MetaDataMapper $metadataMapper,
		private OrganisationMapper $organisationMapper,
		private PublicationMapper $publicationMapper,
		private ThemeMapper $themeMapper,
		private ContainerInterface $container,
		private IAppConfig $config
	) {
		$this->appName = 'opencatalogi';
	}

	/**
	 * Gets an object based on the object type and id.
	 *
	 * @param string $objectType The type of object to retrieve.
	 * @param string $id The id of the object to retrieve.
	 * @param array $extend Additional parameters for extending the query.
	 * @return mixed The retrieved object.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 */
	public function getObject(string $objectType, string $id, array $extend = [])
	{
		// Get the appropriate mapper for the object type
		$mapper = $this->getMapper($objectType);
		// Use the mapper to find and return the object
		return $mapper->find($id);
	}

	/**
	 * Gets objects based on the object type, filters, search conditions, and other parameters.
	 *
	 * @param string $objectType The type of objects to retrieve.
	 * @param int|null $limit The maximum number of objects to retrieve.
	 * @param int|null $offset The offset from which to start retrieving objects.
	 * @param array $filters Filters to apply to the query.
	 * @param array $searchConditions Search conditions to apply to the query.
	 * @param array $searchParams Search parameters for the query.
	 * @param array $sort Sorting parameters for the query.
	 * @return array The retrieved objects.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 */
	public function getObjects(string $objectType, ?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = [], ?array $sort = []): array
	{
		// Get the appropriate mapper for the object type
		$mapper = $this->getMapper($objectType);
		// Use the mapper to find and return the objects based on the provided parameters
		return $mapper->findAll($limit, $offset, $filters, $searchConditions, $searchParams, $sort);
	}

	/**
	 * Gets multiple objects based on the object type and ids.
	 *
	 * @param string $objectType The type of objects to retrieve.
	 * @param array $ids The ids of the objects to retrieve.
	 * @return array The retrieved objects.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 */
	public function getMultipleObjects(string $objectType, array $ids)
	{
		// Get the appropriate mapper for the object type
		$mapper = $this->getMapper($objectType);
		// Use the mapper to find and return multiple objects based on the provided ids
		return $mapper->findMultiple($ids);
	}

	/**
	 * Gets all objects of a specific type.
	 *
	 * @param string $objectType The type of objects to retrieve.
	 * @param int|null $limit The maximum number of objects to retrieve.
	 * @param int|null $offset The offset from which to start retrieving objects.
	 * @return array The retrieved objects.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 */
	public function getAllObjects(string $objectType, ?int $limit = null, ?int $offset = null)
	{
		// Get the appropriate mapper for the object type
		$mapper = $this->getMapper($objectType);
		// Use the mapper to find and return all objects of the specified type
		return $mapper->findAll($limit, $offset);
	}

	/**
	 * Creates a new object or updates an existing one from an array of data.
	 *
	 * @param string $objectType The type of object to create or update.
	 * @param array $object The data to create or update the object from.
	 * @return mixed The created or updated object.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 */
	public function saveObject(string $objectType, array $object)
	{
		// Get the appropriate mapper for the object type
		$mapper = $this->getMapper($objectType);
		// If the object has an id, update it; otherwise, create a new object
		if (isset($object['id'])) {
			return $mapper->updateFromArray($object['id'], $object);
		}
		else {
			return $mapper->createFromArray($object);
		}
	}

	/**
	 * Deletes an object based on the object type and id.
	 *
	 * @param string $objectType The type of object to delete.
	 * @param string|int $id The id of the object to delete.
	 * @return bool True if the object was successfully deleted, false otherwise.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 */
	public function deleteObject(string $objectType, string|int $id): bool
	{
		// Get the appropriate mapper for the object type
		$mapper = $this->getMapper($objectType);
		// Use the mapper to delete the object
		return $mapper->delete($id);
	}

	/**
	 * Gets the appropriate mapper based on the object type.
	 *
	 * @param string $objectType The type of object to retrieve the mapper for.
	 * @return mixed The appropriate mapper.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 * @throws \Exception If OpenRegister service is not available or if register/schema is not configured.
	 */
	private function getMapper(string $objectType)
	{
		// Get the source for the object type from the configuration
		$source = $this->config->getValueString($this->appName, $objectType . '_source', 'internal');

		// If the source is 'open_registers', use the OpenRegister service
		if($source === 'open_registers') {
			$openRegister = $this->getOpenRegister();
			if($openRegister === null) {
				throw new \Exception("OpenRegister service not available");
			}
			$register = $this->config->getValueString($this->appName, $objectType . '_register', '');
			if(empty($register)) {
				throw new \Exception("Register not configured for $objectType");
			}
			$schema = $this->config->getValueString($this->appName, $objectType . '_schema', '');
			if(empty($schema)) {
				throw new \Exception("Schema not configured for $objectType");
			}
			return $openRegister->getMapper($register, $schema);
		}

		// If the source is internal, return the appropriate mapper based on the object type
		switch ($objectType) {
			case 'attachment':
				return $this->attachmentMapper;
			case 'catalog':
				return $this->catalogMapper;
			case 'listing':
				return $this->listingMapper;
			case 'metadata':
				return $this->metadataMapper;
			case 'organisation':
				return $this->organisationMapper;
			case 'publication':
				return $this->publicationMapper;
			case 'theme':
				return $this->themeMapper;
			default:
				throw new \InvalidArgumentException("Unknown object type: $objectType");
		}
	}

	/**
	 * Attempts to retrieve the OpenRegister service from the container.
	 *
	 * @return mixed|null The OpenRegister service if available, null otherwise.
	 */
	public function getOpenRegister()
	{
		try {
			// Attempt to get the OpenRegister service from the container
			return $this->container->get('OCA\OpenRegister\Service\ObjectService');
		} catch (\Exception $e) {
			// If the service is not available, return null
			return null;
		}
	}
	
}
