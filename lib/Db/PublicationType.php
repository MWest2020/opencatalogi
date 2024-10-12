<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class PublicationType extends Entity implements JsonSerializable
{
	protected ?string $uuid        = null;
	protected ?string $version     = '0.0.1';
	protected ?string $title       = null;
	protected ?string $description = null;
	protected ?array  $required    = null;
	protected ?array  $properties  = null;
	protected ?string $source      = null;
	protected ?string $summary     = null;
	protected ?array  $archive     = null;
	protected ?DateTime $updated   = null;
	protected ?DateTime $created   = null;

	public function __construct() {
		$this->addType(fieldName: 'uuid', type: 'string');
		$this->addType(fieldName: 'version', type: 'string');
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'required', type: 'json');
		$this->addType(fieldName: 'properties', type: 'json');
		$this->addType(fieldName: 'source', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'archive', type: 'json');
		$this->addType(fieldName: 'updated', type: 'datetime');
		$this->addType(fieldName: 'created', type: 'datetime');

		// Set the source URL using the id if available
		$this->setSourceUrl();
	}

	// Override setId method to update the source URL when id is set
	public function setId(int $id): void {
		parent::setId($id);
		$this->setSourceUrl();
	}

	// Method to set the source URL dynamically based on the entity id and Nextcloud's domain
	public function setSourceUrl(): void {
		// Get the current URL of the Nextcloud installation
		$baseUrl = $this->getBaseUrl();

		// Set the source dynamically if the id is available
		if ($this->id !== null) {
			$this->setSource($baseUrl . '/index.php/apps/opencatalogi/api/publicationtype/' . $this->id);
		}
	}

	// Method to get the base URL of the Nextcloud installation
	private function getBaseUrl(): string {
		// Determine the scheme (http or https)
		$scheme = $this->isHttps() ? 'https' : 'http';
		// Get the server host and port using $_SERVER
		$host = $_SERVER['SERVER_NAME'] ?? 'localhost';
		$port = $_SERVER['SERVER_PORT'] ?? 80;

		// Construct the base URL (including port if it's not default HTTP/HTTPS ports)
		$baseUrl = $scheme . '://' . $host;
		if (($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
			$baseUrl .= ':' . $port;
		}

		return $baseUrl;
	}

	// Check if the current connection is HTTPS
	private function isHttps(): bool {
		return (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off');
	}

	public function setSource(?string $source): self {
		if ($source === null) {
			$this->setSourceUrl();
		}
		$this->source = $source;

		return $this;
	}

	public function getJsonFields(): array
	{
		return array_keys(
			array_filter($this->getFieldTypes(), function ($field) {
				return $field === 'json';
			})
		);
	}

	public function hydrate(array $object): self
	{
		$jsonFields = $this->getJsonFields();

		// Remove any fields that start with an underscore
		// These are typically internal fields that shouldn't be updated directly
		foreach ($object as $key => $value) {
			if (str_starts_with($key, '_')) {
				unset($object[$key]);
			}
		}

		foreach ($object as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === []) {
				$value = null;
			}

			$method = 'set' . ucfirst($key);

			try {
				$this->$method($value);
			} catch (\Exception $exception) {
				// Handle or log the exception as needed
			}
		}

		return $this;
	}

	public function jsonSerialize(): array
	{
		$properties = [];
		foreach ($this->properties ?? [] as $key => $property) {
			$properties[$key] = $property;
			if (isset($property['type']) === false) {
				$properties[$key] = $property;
				continue;
			}
			switch ($property['format'] ?? '') {
				case 'string':
				case 'array':
					$properties[$key]['default'] = (string) ($property['default'] ?? '');
					break;
				case 'int':
				case 'integer':
				case 'number':
					$properties[$key]['default'] = (int) ($property['default'] ?? 0);
					break;
				case 'bool':
					$properties[$key]['default'] = (bool) ($property['default'] ?? false);
					break;
			}
		}

		if (empty($this->source) === true) {
			$this->setSourceUrl();
		}

		$array = [
			'id'          => $this->id,
			'uuid'        => $this->uuid,
			'version'     => $this->version,
			'title'       => $this->title,
			'description' => $this->description,
			'required'    => $this->required,
			'properties'  => $properties,
			'source'      => $this->source,
			'summary'     => $this->summary,
			'archive'     => $this->archive,
			'updated'     => $this->updated?->format('c'),
			'created'     => $this->created?->format('c'),
		];

		$jsonFields = $this->getJsonFields();

		foreach ($array as $key => $value) {
			if (in_array($key, $jsonFields) === true && $value === null) {
				$array[$key] = [];
			}
		}

		return $array;
	}
}
