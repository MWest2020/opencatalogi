<?php

namespace OCA\OpenCatalogi\Service;

use Adbar\Dot;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use OCP\App\IAppManager;
use Symfony\Component\Uid\Uuid;
use Psr\Container\ContainerInterface;
use OCP\IAppConfig;
// Lets grab the mappers
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCA\OpenCatalogi\Db\PublicationTypeMapper;
use OCA\OpenCatalogi\Db\OrganizationMapper;
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
	 * @param PublicationTypeMapper $publicationTypeMapper Mapper for publication types
	 * @param OrganizationMapper $organizationMapper Mapper for organizations
	 * @param PublicationMapper $publicationMapper Mapper for publications
	 * @param ThemeMapper $themeMapper Mapper for themes
	 * @param ContainerInterface $container Container for dependency injection
	 * @param IAppConfig $config App configuration
	 */
	public function __construct(
		private AttachmentMapper $attachmentMapper,
		private CatalogMapper $catalogMapper,
		private ListingMapper $listingMapper,
		private PublicationTypeMapper $publicationTypeMapper,
		private OrganizationMapper $organizationMapper,
		private PublicationMapper $publicationMapper,
		private ThemeMapper $themeMapper,
		private ContainerInterface $container,
		private readonly IAppManager $appManager,
		private readonly IAppConfig $config,
	) {
		$this->appName = 'opencatalogi';
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
			case 'publicationType':	
				return $this->publicationTypeMapper;
			case 'organization':
				return $this->organizationMapper;
			case 'publication':
				return $this->publicationMapper;
			case 'theme':
				return $this->themeMapper;
			default:
				throw new \InvalidArgumentException("Unknown object type: $objectType");
		}
	}

	// Frome here on all the generic methods for the objects are defined, these methods are used by the controllers to get the objects and shoudl not be hanges in impelementation

	/**
	 * Gets an object based on the object type and id.
	 *
	 * @param string $objectType The type of object to retrieve.
	 * @param string $id The id of the object to retrieve.
	 * @param array $extend Additional parameters for extending the query.
	 * @return mixed The retrieved object.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 */
	public function getObject(string $objectType, string $id)
	{
		// Clean up the id if it's a URI by getting only the last path part
		if (filter_var($id, FILTER_VALIDATE_URL)) {
			$parts = explode('/', rtrim($id, '/'));
			$id = end($id);
		}

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
	 * @return array The retrieved objects as arrays.
	 * @throws \InvalidArgumentException If an unknown object type is provided.
	 */
	public function getObjects(
		string $objectType, 
		?int $limit = null, 
		?int $offset = null, 
		?array $filters = [], 
		?array $searchConditions = [], 
		?array $searchParams = [], 
		?array $sort = [],
		?array $extend = []
	): array
	{
		// Get the appropriate mapper for the object type
		$mapper = $this->getMapper($objectType);
		// Use the mapper to find and return the objects based on the provided parameters
		$objects = $mapper->findAll($limit, $offset, $filters, $searchConditions, $searchParams, $sort);
		
		// Convert entity objects to arrays using jsonSerialize
		$objects = array_map(function($object) {
			return $object->jsonSerialize();
		}, $objects);
		
		// Extend the objects if the extend array is not empty	
		if(!empty($extend)) {
			$objects = array_map(function($object) use ($extend) {
				return $this->extendEntity($object, $extend);
			}, $objects);
		}
		
		return $objects;
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
		// Clean up the ids if they are URIs
		$cleanedIds = array_map(function($id) {
			// If the id is a URI, get only the last part of the path
			if (filter_var($id, FILTER_VALIDATE_URL)) {
				$parts = explode('/', rtrim($id, '/'));
				return end($parts);
			}
			return $id;
		}, $ids);

		// Get the appropriate mapper for the object type
		$mapper = $this->getMapper($objectType);
		// Use the mapper to find and return multiple objects based on the provided cleaned ids
		return $mapper->findMultiple($cleanedIds);
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
		$objects = $mapper->findAll($limit, $offset);
		
		return $objects;
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
	 * Attempts to retrieve the OpenRegister service from the container.
	 *
	 * @return mixed|null The OpenRegister service if available, null otherwise.
	 */
	public function getOpenRegisters(): ?\OCA\OpenRegister\Service\ObjectService
	{
		if(in_array(needle: 'openregister', haystack: $this->appManager->getInstalledApps()) === true) {
			try {
				// Attempt to get the OpenRegister service from the container
				return $this->container->get('OCA\OpenRegister\Service\ObjectService');
			} catch (\Exception $e) {
				// If the service is not available, return null
				return null;
			}
		}

		return null;
	}

	/**
	 * Get a result array for a request based on the request and the object type.
	 *
	 * @param string $objectType The type of object to retrieve
	 * @param array $requestParams The request parameters
	 * @return array The result array containing objects and total count
	 */
	public function getResultArrayForRequest(string $objectType, array $requestParams): array
	{
		// Extract specific parameters
		$limit = $requestParams['limit'] ?? $requestParams['_limit'] ?? null;
		$offset = $requestParams['offset'] ?? $requestParams['_offset'] ?? null;
		$order = $requestParams['order'] ?? $requestParams['_order'] ?? null;
		$extend = $requestParams['extend'] ?? $requestParams['_extend'] ?? null;		

		// Ensure order and extend are arrays
		if (is_string($order)) {
			$order = array_map('trim', explode(',', $order));
		}
		if (is_string($extend)) {
			$extend = array_map('trim', explode(',', $extend));
		}

		// Remove unnecessary parameters from filters
		$filters = $requestParams;
		unset($filters['_route']); // TODO: Investigate why this is here and if it's needed
		unset($filters['_extend'], $filters['_limit'], $filters['_offset'], $filters['_order']);
		unset($filters['extend'], $filters['limit'], $filters['offset'], $filters['order']);

		// Fetch objects based on filters and order
		$objects = $this->getObjects($objectType, null, null, $filters, $limit, $offset, $order, $extend);

		// Extend the objects if the extend array is not empty	
		if(!empty($extend)) {
			$objects = array_map(function($object) use ($extend) {
				return $this->extendEntity($object, $extend);
			}, $objects);
		}

		// Prepare response data
		return [
			'results' => $objects,
			'total' => count($objects)
		];
	}

	/**
	 * Extends an entity with related objects based on the extend array.
	 *
	 * @param mixed $entity The entity to extend
	 * @param array $extend An array of properties to extend
	 * @return array The extended entity as an array
	 * @throws \Exception If a property is not present on the entity
	 */
	public function extendEntity($entity, array $extend): array
	{
		// Convert the entity to an array if it's not already one
		$result = is_array($entity) ? $entity : $entity->jsonSerialize();

		// Iterate through each property to be extended
		foreach ($extend as $property) {
			// Create a singular property name
			$singularProperty = rtrim($property, 's');

			// Check if property or singular property are keys in the array
			if (array_key_exists($property, $result)) {
				$value = $result[$property];
			} elseif (array_key_exists($singularProperty, $result)) {
				$value = $result[$singularProperty];
			} else {
				throw new \Exception("Property '$property' or '$singularProperty' is not present in the entity.");
			}
			
			// Get a mapper for the property
			$propertyObject = $property;
			try {
				$mapper = $this->getMapper($property);
				$propertyObject = $singularProperty;
			} catch (\Exception $e) {
				try {
					$mapper = $this->getMapper($singularProperty);
					$propertyObject = $singularProperty;
				} catch (\Exception $e) {
					// If still no mapper, throw a no mapper available error
					throw new \Exception("No mapper available for property '$property'.");
				}
			}

			// Update the values
			if (is_array($value)) {
				// If the value is an array, get multiple related objects
				$result[$property] = $this->getMultipleObjects($propertyObject, $value);
			} else {
				// If the value is not an array, get a single related object
				$result[$property] = $this->getObject($propertyObject, $value);
			}
		}

		// Return the extended entity as an array
		return $result;
	}
}
