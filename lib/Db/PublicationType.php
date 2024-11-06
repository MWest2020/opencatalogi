<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;
use OCP\IURLGenerator;

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

	public function getSchema(IURLGenerator $urlGenerator): object
	{
		$schema = [];
		$schema['$schema']  = 'https://json-schema.org/draft/2020-12/schema';
		$schema['$id']      = $urlGenerator->getAbsoluteURL($urlGenerator->linkToRoute('openregister.Schemas.show', ['id' => $this->getUuid()]));
		$schema['type']     = 'object';
		$schema['required'] = [];
		$schema['properties'] = [];

		foreach($this->getProperties() as $name => $property) {
			if ($property['required'] === true) {
				$schema['required'][] = $name;
			}
			unset($property['title'], $property['required']);

			// Remove empty fields with array_filter(), and add it to the properties of the schema.
			$schema['properties'][$name] = array_filter($property);
		}

		return json_decode(json_encode($schema));
	}
}
