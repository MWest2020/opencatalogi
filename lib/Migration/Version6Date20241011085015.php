<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\OpenCatalogi\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * FIXME Auto-generated migration step: Please modify to your needs!
 */
class Version6Date20241011085015 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		/**
		 * @var ISchemaWrapper $schema
		 */
		$schema = $schemaClosure();

		if($schema->hasTable(tableName: 'ocat_publications') === false) {
			$table = $schema->createTable(tableName: 'ocat_publications');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'reference', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn(name: 'summary', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn(name: 'description', typeName: TYPES::STRING, options: [
				'notnull' => false,
				'length' => 20000
			]);
			$table->addColumn(name: 'image', typeName: TYPES::STRING, options: [
				'length' => 255,
				'notnull' => false,
			]);
			$table->addColumn(name: 'category', typeName: TYPES::STRING, options: [
				'length' => 255,
				'notnull' => true,
			]);
			$table->addColumn(name: 'portal', typeName: TYPES::STRING);
			$table->addColumn(name: 'catalogi', typeName: TYPES::STRING);
			$table->addColumn(name: 'publication_type', typeName: TYPES::STRING);
			$table->addColumn(name: 'modified', typeName: TYPES::DATETIME);
			$table->addColumn(name: 'featured', typeName: TYPES::BOOLEAN, options: [
				'notnull' => false,
			]
			);
			$organization = $table->addColumn(name: 'organization', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$organization->setDefault('{}');
			$data = $table->addColumn(name: 'data', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$data->setDefault('{}');
			$attachments = $table->addColumn(name: 'attachments', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$attachments->setDefault('{}');
			$table->addColumn(name: 'attachment_count', typeName: TYPES::INTEGER);
			$table->addColumn(name: 'schema', typeName: TYPES::STRING);
			$table->addColumn(name: 'status', typeName: TYPES::STRING);
			$table->addColumn(name: 'license', typeName: TYPES::STRING);
			$table->addColumn(name: 'themes', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'anonymization', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'language_object', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$published = $table->addColumn(name: 'published', typeName: Types::DATETIME);
			$published->setDefault(default: null);
			$published->setNotnull(notnull: false);

			$table->setPrimaryKey(columnNames: ['id']);

		}

		if($schema->hasTable(tableName: 'ocat_catalogi') === false) {
			$table = $schema->createTable(tableName: 'ocat_catalogi');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'summary', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn(name: 'description', typeName: TYPES::STRING, options: [
				'notnull' => false,
				'length' => 20000
			]);
			$table->addColumn(name: 'image', typeName: TYPES::STRING, options: [
				'length' => 255,
				'notnull' => false,
			]);
			$table->addColumn(name: 'search', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'listed', typeName: Types::BOOLEAN, options: [
				'notNull' => false,
				'default' => false
			]);
			$publicationType = $table->addColumn(
				name: 'publication_type',
				typeName: Types::JSON,
				options: [
					'notNull' => false,
				]);
			$publicationType->setDefault('{}');
			$table->addColumn(
				name: 'organisation',
				typeName: Types::STRING,
				options: [
					'notNull' => false,
					'default' => null
				]);

			$table->setPrimaryKey(columnNames: ['id']);

		}

		if($schema->hasTable(tableName: 'ocat_publication_type') === false) {
			$table = $schema->createTable(tableName: 'ocat_publication_type');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'version', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'description', typeName: TYPES::STRING, options: [
				'notnull' => false,
				'length' => 20000
			]);
			$table->addColumn(name: 'required', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'properties', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(
				name: 'source',
				typeName: Types::STRING,
				options: [
					'notNull' => false,
					'default' => null
				]);
			if($table->hasColumn(name: 'summary') === false) {
				$column = $table->addColumn(name: 'summary', typeName: Types::STRING);
				$column->setNotnull(notnull: false)->setDefault(default: null);
			}

			if($table->hasColumn(name: 'archive') === false) {
				$column = $table->addColumn(name: 'archive', typeName: Types::JSON);
				$column->setNotnull(notnull: false)->setDefault(default: null);
			}

			$table->setPrimaryKey(columnNames: ['id']);

		}

		if($schema->hasTable(tableName: 'ocat_listings') === false) {
			$table = $schema->createTable(tableName: 'ocat_listings');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'summary', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn(name: 'description', typeName: TYPES::STRING, options: [
				'notnull' => false,
				'length' => 20000
			]);
			$table->addColumn(name: 'search', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'directory', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'publication_type', typeName: Types::JSON, options: [
				'notnull' => false,
				'default' => '{}'
			]);
			$table->addColumn(name: 'status', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'last_sync', typeName: TYPES::DATETIME, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'default', typeName: TYPES::BOOLEAN, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'available', typeName: TYPES::BOOLEAN, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'catalog_id', typeName: Types::STRING);
			$table->addColumn(name: 'status_code', typeName: Types::INTEGER)
				->setNotnull(notnull: false)
				->setDefault(default: null);


			$table->setPrimaryKey(columnNames: ['id']);

		}

		if($schema->hasTable(tableName: 'ocat_organizations') === false) {
			$table = $schema->createTable(tableName: 'ocat_organizations');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'summary', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn(name: 'description', typeName: TYPES::STRING, options: [
				'notnull' => false,
				'length' => 20000
			]);
			$table->addColumn(name: 'image', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'oin', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'tooi', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'rsin', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'pki', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);

			$table->setPrimaryKey(columnNames: ['id']);

		}

		if($schema->hasTable(tableName: 'ocat_attachments') === false) {
			$table = $schema->createTable(tableName: 'ocat_attachments');
			$table->addColumn(name: 'id', typeName: Types::BIGINT, options: [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 4,
			]);
			$table->addColumn(name: 'reference', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn(name: 'title', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn(name: 'summary', typeName: TYPES::STRING, options: [
				'notnull' => true,
				'length' => 255
			]);
			$table->addColumn(name: 'description', typeName: TYPES::STRING, options: [
				'notnull' => false,
				'length' => 20000
			]);
			$table->addColumn(name: 'labels', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'access_url', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'download_url', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'type', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'extension', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'size', typeName: TYPES::INTEGER, options: [
				'notnull' => true,
				'default' => 0,
			]);
			$table->addColumn(name: 'version_of', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'hash', typeName: TYPES::STRING, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'anonymization', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'language', typeName: TYPES::JSON, options: [
				'notnull' => false,
			]);
			$table->addColumn(name: 'published', typeName: TYPES::DATETIME, options: [
				'notNull' => false
			]);
			$table->addColumn(name: 'modified', typeName: TYPES::DATETIME, options: [
				'notNull' => false
			]);
			$table->addColumn(name: 'license', typeName: TYPES::STRING);


			$table->setPrimaryKey(columnNames: ['id']);

		}
		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}
}
