<?php

namespace OCA\OpenCatalogi\Db;

use DateTime;
use JsonSerializable;
use OCP\AppFramework\Db\Entity;

class Publication extends Entity implements JsonSerializable
{
	protected ?string $uuid = null;
	protected ?string $version = '0.0.1';
	protected ?string $title = null;
	protected ?string $reference = null;
	protected ?string $summary = null;
	protected ?string $description = null;
	protected ?string $image = null;
	protected ?string $category = null;
	protected ?string $portal = null;
	protected ?string $catalogId = null;
	protected ?string $publicationType = null;
	protected ?DateTime $modified = null;
	protected ?bool $featured = false;
	protected ?array $organization = [];
	protected ?array $data = [];
	protected ?array $attachments = [];
	protected int $attachmentCount = 0;
	protected ?string $schema = null;
	protected ?string $status = null;
	protected ?string $license = null;
	protected ?array $themes = [];
	protected ?array $anonymization = [];
	protected ?array $languageObject = [];
	protected ?DateTime $published = null;
	protected ?DateTime $updated = null;
	protected ?DateTime $created = null;

	public function __construct() {
		$this->addType(fieldName: 'uuid', type: 'string');
		$this->addType(fieldName: 'version', type: 'string');
		$this->addType(fieldName: 'title', type: 'string');
		$this->addType(fieldName: 'reference', type: 'string');
		$this->addType(fieldName: 'summary', type: 'string');
		$this->addType(fieldName: 'description', type: 'string');
		$this->addType(fieldName: 'image', type: 'string');
		$this->addType(fieldName: 'category', type: 'string');
		$this->addType(fieldName: 'portal', type: 'string');
		$this->addType(fieldName: 'catalogId', type: 'string');
		$this->addType(fieldName: 'publicationType', type: 'string');
		$this->addType(fieldName: 'modified', type: 'datetime');
		$this->addType(fieldName: 'featured', type: 'boolean');
		$this->addType(fieldName: 'organization', type: 'json');
		$this->addType(fieldName: 'data', type: 'json');
		$this->addType(fieldName: 'attachments', type: 'json');
		$this->addType(fieldName: 'attachmentCount', type: 'integer');
		$this->addType(fieldName: 'schema', type: 'string');
		$this->addType(fieldName: 'status', type: 'string');
		$this->addType(fieldName: 'license', type: 'string');
		$this->addType(fieldName: 'themes', type: 'json');
		$this->addType(fieldName: 'anonymization', type: 'json');
		$this->addType(fieldName: 'languageObject', type: 'json');
		$this->addType(fieldName: 'published', type: 'datetime');
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

		$this->setStatus('Concept');
		$this->setAttachments(null);
		$this->setOrganization(null);
		$this->setData(null);
		$this->setModified(new DateTime());

		// Remove any fields that start with an underscore
		// These are typically internal fields that shouldn't be updated directly
		foreach ($object as $key => $value) {
			if (str_starts_with($key, '_')) {
				unset($object[$key]);
			}
		}

		if (!isset($object['published'])) {
			$object['published'] = null;
		}

		// Todo: publicationType is deprecated, we should use Schema instead. But this needs front-end changes as well.
		if (empty($object['schema'])) {
			$object['schema'] = $object['publicationType'] ?? $this->getPublicationType();
		}

		foreach ($object as $key => $value) {
			if (in_array($key, $jsonFields) && $value === []) {
				$value = null;
			}

			$method = 'set' . ucfirst($key);

			try {
				$this->$method($value);
			} catch (\Exception $exception) {
				// Handle or log the exception as needed
			}
		}

		$this->setAttachmentCount(0);
		if ($this->attachments !== null) {
			$this->setAttachmentCount(count($this->getAttachments()));
		}

		return $this;
	}

	public function jsonSerialize(): array
	{
		$array = [
			'id' => $this->id,
			'uuid' => $this->uuid,
			'version' => $this->version,
			'title' => $this->title,
			'reference' => $this->reference,
			'summary' => $this->summary,
			'description' => $this->description,
			'image' => $this->image,
			'category' => $this->category,
			'portal' => $this->portal,
			'catalogId' => $this->catalogId,
			'publicationType' => $this->publicationType,
			'modified' => $this->modified?->format('c'),
			'featured' => $this->featured,
			'organization' => $this->organization,
			'data' => $this->data,
			'attachments' => $this->attachments,
			'attachmentCount' => $this->attachmentCount,
			'schema' => $this->schema,
			'status' => $this->status,
			'license' => $this->license,
			'themes' => $this->themes,
			'anonymization' => $this->anonymization,
			'languageObject' => $this->languageObject,
			'published' => $this->published?->format('c'),
			'updated' => $this->updated?->format('c'),
			'created' => $this->created?->format('c'),
		];

		$jsonFields = $this->getJsonFields();

		foreach ($array as $key => $value) {
			if (in_array($key, $jsonFields) && $value === null) {
				$array[$key] = [];
			}
		}

		return $array;
	}

	public function getStatus(): ?string {
		return $this->status;
	}
}
