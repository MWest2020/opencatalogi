<?php

namespace OCA\OpenCatalogi\Service;

use DateTime;
use GuzzleHttp\Client;
use OCA\OpenCatalogi\Db\Catalog;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCP\IAppConfig;
use OCP\IURLGenerator;

class DirectoryService
{
	private string $appName = 'opencatalogi';
	private Client $client;

	public function __construct(
		private readonly IURLGenerator $urlGenerator,
		private readonly IAppConfig $config,
		private readonly ObjectService $objectService,
		private readonly CatalogMapper $catalogMapper,
		private readonly ListingMapper $listingMapper,
	)
	{
		$this->client = new Client([]);
	}

	private function getDirectoryEntry(string $catalogId): array
	{
		$now = new DateTime();
		return [
			'title' => '',
			'summary' => '',
			'description' => '',
			'search'	=> $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.search.index")),
			'directory'	=> $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index")),
			'publicationType'	=> '',
			'status'	=> '',
			'lastSync'	=> $now->format(format: 'c'),
			'default'	=> true,
			'catalogId' => $catalogId,
			'reference' => '',
		];
	}

	public function registerToExternalDirectory (array $newDirectory = [], ?string $url = null, array &$externalDirectories = []): int
	{
		
		if ($result !== null) {
			return $result->getStatusCode();
		}
		return 200;

	}


	/**
	 * array_map function for fetching a directory for a listing.
	 *
	 * @param Listing|array $listing
	 * @return array
	 */
	private function getDirectoryFromListing($listing): array
	{
		// Serialize the listing if it's a Listing object
		if ($listing instanceof Listing) {
			$listing = $listing->jsonSerialize();
		}

		$listing['id'] = $listing['uuid'];
		// Remove unneeded fields
		unset($listing['status']);
		unset($listing['lastSync']);
		unset($listing['default']);
		unset($listing['available']);
		unset($listing['catalogId']);
		unset($listing['statusCode']);
		unset($listing['uuid']);

		// todo: This should be mapped to the stoplight documentation	
		return $listing;
	}
	
	/**
	 * array_map function for fetching a directory for a catalog.
	 *
	 * @param Catalog|array $listing
	 * @return string
	 */
	private function getDirectoryFromCatalog($catalog): array
	{
		// Serialize the listing if it's a Listing object
		if ($catalog instanceof Catalog) {
			$catalog = $catalog->jsonSerialize();
		}

		$catalog['id'] = $catalog['uuid'];
		// Remove unneeded fields
		unset($catalog['image']);
		unset($catalog['uuid']);
		// Keep $catalog['listed'] as it is neededed later on to filter out the catalogi that are not listed!
		// Add the search and directory urls
		$catalog['search'] = $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.search.index"));
		$catalog['directory'] = $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index"));
		// todo: This should be mapped to the stoplight documentation	
		return $catalog;
	}

	/**
	 * Get all directories to scan.
	 *
	 * @return array
	 */
	public function getDirectories(): array
	{ 
		// Lets first get all the listings
		$listings = $this->objectService->getObjects(
			objectType: 'listing', 
			extend: ['publicationTypes','organization']);
		$listings = array_map(callback: [$this, 'getDirectoryFromListing'], array: $listings);

		// todo: define when a listed item should not be shown (e.g. when secret or trusted is true), this is a product decision
		
		// Then all the catalogi
		$catalogi = $this->objectService->getObjects(
			objectType: 'catalog', 
			extend: ['publicationTypes','organization']
		);
		$catalogi = array_map(callback: [$this, 'getDirectoryFromCatalog'], array: $catalogi);
		
		// Filter out the catalogi that are not listed
		$catalogi = array_filter($catalogi, function($catalog) {
			return $catalog['listed'] !== false;
		});

		// Remove the 'listed' property from all catalogi objects
		$catalogi = array_map(function($catalog) {
			unset($catalog['listed']);
			return $catalog;
		}, $catalogi);
		
		
		// Merge listings and catalogi into a new array
		$mergedDirectories = array_merge($listings, $catalogi);
		
		// Create a wrapper array with 'results' and 'total'
		$directories = [
			'results' => $mergedDirectories,
			'total' => count($mergedDirectories)
		];

		return array_unique(array: $directories);
	}

	/**
	 * Run a synchronisation based on cron
	 *
	 * @return array
	 */
	public function doCronSync(): array {

		$results = [];
		$directories = $this->getDirectories();
		foreach ($directories as $key=>$directory){
			$result = $this->syncExternalDirectory(url: $directory);
			$results = array_merge_recursive($results, $result);
		}

		return $results;
	}

	/**
	 * Validate an external listing.
	 *
	 * @param array $listing
	 * @return bool
	 */
	public function validateExternalListing(array $listing): bool
	{
		// Remove the id field from the listing
		unset($listing['id']);
		// TODO: Implement validation logic here
		return true;
	}

	public function syncExternalDirectory(string $url): array
	{
		$result = $this->client->get(uri: $url);
		// Lets fallback to the /api/directory endpoint if the result is not JSON

		if (str_contains(haystack: $result->getHeader('Content-Type')[0], needle: 'application/json') === false) {
			$url = rtrim(string: $url, characters: '/').'/apps/opencatalogi/api/directory';
			$result = $this->client->get(uri: $url);
		}

		$results = json_decode(json: $result->getBody()->getContents(), associative: true);
		$addedListings = [];
		$updatedListings = [];

		foreach ($results['results'] as $listing) {
			// Lets validate the listing
			if($this->validateExternalListing(listing: $listing) === true) {
				continue;
			}
			// Lest see if we already have this listing
			// Todo this is tricky couse it requiers a local database call so wont work with open registers
			if($this->listingMapper->findByCatalogIdAndDirectory(catalogId: $listing['uuid'], directory: $listing['directory']) !== null) {
				// @TODO: We should maybe update the listing instead of skipping it
				$updatedListings[] = $listing['directory'].'/'.$listing['uuid'];
				continue;		
			}

			$listing = $this->objectService->saveObject('listing', $listing);
			$addedListings[] = $listing['directory'].''.$listing['uuid'];
		}

		return [
			'addedListings' => $addedListings,
			'updatedListings' => $updatedListings,
			'total' => count($addedListings) + count($updatedListings)
		];
	}

	public function addListingFromExternalDirectory(string $url): void
	{
	}

	// Get or update the data for an specifi exernal directory
	public function fetchFromExternalDirectory(array $directory = [], ?string $url = null, bool $update = false): array
	{
		if ($directory !== [] && $url === null) {
			$url = $directory['directory'];
		}
 		$result = $this->client->get(uri: $url);

		if (str_contains(haystack: $result->getHeader('Content-Type')[0], needle: 'application/json') === false) {
			$url = rtrim(string: $url, characters: '/').'/apps/opencatalogi/api/directory';
			$result = $this->client->get(uri: $url);
		}

		$results = json_decode(json: $result->getBody()->getContents(), associative: true);

		$addedDirectories = [];
		$catalogs 		  = [];

		foreach ($results['results'] as $record) {
			$catalogs[] = $record['catalogId'];
			$addedDirectories[] = $this->createDirectoryFromResult(result: $record, update: $update);

		}


		$localListings = $this->listingMapper->findAll(filters: ['directory' => $url]);

		foreach ($localListings as $localListing) {
			if (in_array(needle: $localListing->getCatalogId(), haystack: $catalogs) === false) {
				$this->listingMapper->delete($localListing);
			}
		}

		return $addedDirectories;
	}

	public function updateToExternalDirectory(): array
	{
		return [];
	}

	public function listDirectory(array $filters = [], int $limit = 30, int $offset = 0): array
	{
		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$filters['catalog_id'] = $filters['catalogId'];
			unset($filters['catalogId']);

			return $this->listingMapper->findAll(limit: $limit, offset: $offset, filters: $filters);
		}
		$filters['_schema'] = 'directory';

		$dbConfig['base_uri'] = $this->config->getValueString(app: $this->appName, key: 'mongodbLocation');
		$dbConfig['headers']['api-key'] = $this->config->getValueString(app: $this->appName, key: 'mongodbKey');
		$dbConfig['mongodbCluster'] = $this->config->getValueString(app: $this->appName, key: 'mongodbCluster');

		return $this->objectService->findObjects(filters: $filters, config: $dbConfig)['documents'];
	}

	public function deleteListing(string $catalogId, string $directoryUrl): void
	{
		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$results = $this->listingMapper->findAll(filters: ['directory' => $directoryUrl, 'catalog_id' => $catalogId]);

			foreach ($results as $result) {
				$this->listingMapper->delete(entity: $result);
			}

			return;
		}
		$dbConfig = [
			'base_uri' => $this->config->getValueString(app: $this->appName, key: 'mongodbLocation'),
			'headers' => ['api-key' => $this->config->getValueString(app: $this->appName, key: 'mongodbKey')],
			'mongodbCluster' => $this->config->getValueString(app: $this->appName, key: 'mongodbCluster')
		];

		$results = $this->objectService->findObjects(filters: ['directory' => $directoryUrl, 'catalogId' => $catalogId, '_schema' => 'directory'], config: $dbConfig);

		foreach ($results['documents'] as $result) {
			$this->objectService->deleteObject(filters: ['_id' => $result['_id']], config: $dbConfig);
		}

		return;
	}

	public function directoryExists(string $catalogId, ?array &$listing = null): bool
	{
		$directoryUrl = $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index"));

		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			$results = $this->listingMapper->findAll(filters: ['directory' => $directoryUrl, 'catalog_id' => $catalogId]);

			$result = count($results) > 0;

			if ($result === true) {
				$listing = $results[0]->jsonSerialize();
			}
			return $result;
		}
		$dbConfig = [
			'base_uri' => $this->config->getValueString(app: $this->appName, key: 'mongodbLocation'),
			'headers' => ['api-key' => $this->config->getValueString(app: $this->appName, key: 'mongodbKey')],
			'mongodbCluster' => $this->config->getValueString(app: $this->appName, key: 'mongodbCluster')
		];

		$results = $this->objectService->findObjects(filters: ['directory' => $directoryUrl, 'catalogId' => $catalogId, '_schema' => 'directory'], config: $dbConfig);


		$result =  count(value: $results['documents']) > 0;

		if ($result === true) {
			$listing = $results['documents'][0];
		}

		return $result;
	}

	public function listCatalog (array $catalog): array
	{
		$existingListing = null;

		$catalogId = $catalog['id'];
		if ($catalog['listed'] === false) {
			$this->deleteListing(catalogId: $catalogId, directoryUrl: $this->urlGenerator->getAbsoluteURL(url: $this->urlGenerator->linkToRoute(routeName:"opencatalogi.directory.index")),);
			return $catalog;
		}


		$listing = $this->getDirectoryEntry(catalogId: $catalogId);

		$listing['title']        = $catalog['title'];
		$listing['description']  = $catalog['description'];
		$listing['summary']      = $catalog['summary'];
		$listing['organization'] = $catalog['organization'];
		$listing['publicationType']     = $catalog['publicationType'];

		if ($this->config->hasKey(app: $this->appName, key: 'mongoStorage') === false
			|| $this->config->getValueString(app: $this->appName, key: 'mongoStorage') !== '1'
		) {
			if ($this->directoryExists(catalogId: $catalogId, listing: $existingListing) === true) {
				$listing = $this->listingMapper->updateFromArray(id: $existingListing['id'], object: $listing);
			} else {
				$listing = $this->listingMapper->createFromArray(object: $listing);
			}

			return $catalog;
		}

		try {
			$dbConfig = [
				'base_uri' => $this->config->getValueString(app: $this->appName, key: 'mongodbLocation'),
				'headers' => ['api-key' => $this->config->getValueString(app: $this->appName, key: 'mongodbKey')],
				'mongodbCluster' => $this->config->getValueString(app: $this->appName, key: 'mongodbCluster')
			];

			$listing['_schema'] = 'directory';

			if ($this->directoryExists(catalogId: $catalogId, listing: $existingListing) === true) {
				$returnData = $this->objectService->updateObject(filters: ['id' => $existingListing['id']], update: $listing, config: $dbConfig);
			} else {
				$returnData = $this->objectService->saveObject(data: $listing, config: $dbConfig);
			}
			return $catalog;
		} catch (\Exception $e) {
			$catalog['listed'] = false;
			return $catalog;
		}

	}
}
