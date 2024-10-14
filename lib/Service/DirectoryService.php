<?php

namespace OCA\OpenCatalogi\Service;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use OCA\OpenCatalogi\Db\Catalog;
use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Db\ListingMapper;
use OCP\IAppConfig;
use OCP\IURLGenerator;

/**
 * Service class for handling directory-related operations
 */
class DirectoryService
{
	/** @var string The name of the app */
	private string $appName = 'opencatalogi';

	/** @var Client The HTTP client for making requests */
	private Client $client;

	/**
	 * Constructor for DirectoryService
	 *
	 * @param IURLGenerator $urlGenerator URL generator interface
	 * @param IAppConfig $config App configuration interface
	 * @param ObjectService $objectService Object service for handling objects
	 * @param CatalogMapper $catalogMapper Mapper for catalog objects
	 * @param ListingMapper $listingMapper Mapper for listing objects
	 */
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

	/**
	 * Register to all unique external directories.
	 *
	 * @return array An array of registration results
	 */
	public function updateAllExternalDirectories(): array
	{
		// Get all directories
		$directories = $this->getDirectories();

		// Extract unique directory URLs
		$uniqueDirectories = array_unique(array_column($directories['results'], 'directory'));

		$results = [];

		// Register to each unique directory
		foreach ($uniqueDirectories as $directoryUrl) {
			$statusCode = $this->registerToExternalDirectory($directoryUrl);
			$results[] = [
				'url' => $directoryUrl,
				'statusCode' => $statusCode
			];
		}

		return $results;
	}

	/**
	 * Register the local directory to the external directory.
	 *
	 * @param string $directoryUrl The URL of the external directory.
	 * @return int The status code of the response.
	 * @throws GuzzleException
	 */
	public function updateExternalDirectory(string $directoryUrl): int
	{
		$body = [
			'url' => $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute('opencatalogi.directory.index'))
		];

		try {
			// Send POST request to register
			$response = $this->client->post($directoryUrl, [
				'json' => $body
			]);

			return $response->getStatusCode();
		} catch (\Exception $e) {
			// Log the error or handle it as needed
			return 500; // Return a 500 status code to indicate an error
		}
	}

	/**
	 * Convert a listing object or array to a directory array
	 *
	 * @param Listing|array $listing The listing object or array to convert
	 * @return array The converted directory array
	 */
	private function getDirectoryFromListing($listing): array
	{
		// Serialize the listing if it's a Listing object
		if ($listing instanceof Listing) {
			$listing = $listing->jsonSerialize();
		}

		// Set id to uuid
		$listing['id'] = $listing['uuid'];

		// Remove unneeded fields
		unset($listing['status'], $listing['lastSync'], $listing['default'], $listing['available'], $listing['catalog'], $listing['statusCode'], $listing['uuid'], $listing['hash']);

		// TODO: This should be mapped to the stoplight documentation
		return $listing;
	}

	/**
	 * Convert a catalog object or array to a directory array
	 *
	 * @param Catalog|array $catalog The catalog object or array to convert
	 * @return array The converted directory array
	 */
	private function getDirectoryFromCatalog($catalog): array
	{
		// Serialize the catalog if it's a Catalog object
		if ($catalog instanceof Catalog) {
			$catalog = $catalog->jsonSerialize();
		}

		// Set id to uuid
		$catalog['id'] = $catalog['uuid'];

		// Remove unneeded fields
		unset($catalog['image'], $catalog['uuid']);
		// Keep $catalog['listed'] as it is needed later on to filter out the catalogi that are not listed!

		// Add the search and directory urls
		$catalog['search'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute("opencatalogi.search.index"));
		$catalog['directory'] = $this->urlGenerator->getAbsoluteURL($this->urlGenerator->linkToRoute("opencatalogi.directory.index"));

		// TODO: This should be mapped to the stoplight documentation
		return $catalog;
	}

	/**
	 * Get all directories to scan.
	 *
	 * @return array An array containing 'results' (merged directories) and 'total' count
	 */
	public function getDirectories(): array
	{
		// Get all the listings
		$listings = $this->objectService->getObjects(objectType: 'listing', extend: ['publicationTypes','organization']);
		$listings = array_map([$this, 'getDirectoryFromListing'], $listings);

		// TODO: Define when a listed item should not be shown (e.g. when secret or trusted is true), this is a product decision

		// Get all the catalogi
		$catalogi = $this->objectService->getObjects(objectType: 'catalog',  extend: ['publicationTypes','organization']);
		$catalogi = array_map([$this, 'getDirectoryFromCatalog'], $catalogi);

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

		return array_unique($directories);
	}

	/**
	 * Run a synchronisation based on cron
	 *
	 * @return array An array containing synchronization results
	 * @throws GuzzleException
	 */
	public function doCronSync(): array {
		$results = [];
		$directories = $this->getDirectories();

		// Extract unique directory URLs
		$uniqueDirectories = array_unique(array_column($directories['results'], 'directory'));

		// Sync each unique directory
		foreach ($uniqueDirectories as $directoryUrl) {
			$result = $this->syncExternalDirectory($directoryUrl);
			$results = array_merge_recursive($results, $result);
		}

		return $results;
	}

	/**
	 * Validate an external listing.
	 *
	 * @param array $listing The listing to validate
	 * @return bool True if the listing is valid, false otherwise
	 */
	public function validateExternalListing(array $listing): bool
	{
		// Remove the id field from the listing
		unset($listing['id']);
		// TODO: Implement validation logic here
		return true;
	}

	/**
	 * Update a listing
	 *
	 * @param array $newListing The new listing
	 * @param Listing $oldListing The old listing
	 * @return array The updated listing
	 */
	public function updateListing(array $newListing, Listing $oldListing): array{
		// Lets clear up the new listing
		$allowedProperties = [
			'version',
			'title',
			'summary',
			'description',
			'search',
			'directory',
			'organization',
			'publicationTypes',
		];

		$filteredListing = array_intersect_key($newListing, array_flip($allowedProperties));

		// Lets see if these changed by checking them agains the hash
		$hash = hash('sha256', json_encode($filteredListing));
		if ($hash === $oldListing->getHash()) {
			return $oldListing->jsonSerialize();
		}

		// If we get here, the listing has changed
		$oldListing->hydrate($newListing);
		$listing = $this->objectService->saveObject('listing', $oldListing);
		return $listing->jsonSerialize();
	}

	/**
	 * Synchronize with an external directory
	 *
	 * @param string $url The URL of the external directory
	 * @return array An array containing synchronization results
	 * @throws GuzzleException
	 */
	public function syncExternalDirectory(string $url): array
	{
		// Get the directory data
		$result = $this->client->get($url);

		// Fallback to the /api/directory endpoint if the result is not JSON
		if (str_contains($result->getHeader('Content-Type')[0], 'application/json') === false) {
			$url = rtrim($url, '/').'/apps/opencatalogi/api/directory';
			$result = $this->client->get($url);
		}

		$results = json_decode($result->getBody()->getContents(), true);
		$addedListings = [];
		$updatedListings = [];

		foreach ($results['results'] as $listing) {
			// Validate the listing
			if (!$this->validateExternalListing($listing)) {
				continue;
			}

			// Check if we already have this listing
			// TODO: This is tricky because it requires a local database call so won't work with open registers
			$oldListing = $this->listingMapper->findByCatalogIdAndDirectory($listing['uuid'], $listing['directory']);
			if ($oldListing !== null) {
				$this->updateListing($listing, $oldListing);
				$updatedListings[] = $listing['directory'].'/'.$listing['uuid'];
				continue;
			}

			// Save the new listing
			$listing = $this->objectService->saveObject('listing', $listing);
			$addedListings[] = $listing['directory'].'/'.$listing['uuid'];
		}

		return [
			'addedListings' => $addedListings,
			'updatedListings' => $updatedListings,
			'total' => count($addedListings) + count($updatedListings)
		];
	}

	public function synchronise(?string $id = null): array
	{
		// Fetch the listing object by its ID
		$object = $this->objectService->getObject('listing', $id);

		$url = $object['directory'];

//		$this->fetchFromExternalDirectory(url: $url, update: true);

		return $object;
	}
}
