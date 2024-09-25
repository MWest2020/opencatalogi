<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2024 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\OpenCatalogi\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * FIXME Auto-generated migration step: Please modify to your needs!
 */
class Version6Date20240923093806 extends SimpleMigrationStep {

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

		if($schema->hasTable(tableName: 'publications') === true) {
			$table = $schema->getTable(tableName: 'publications');
			if($table->hasColumn(name: 'description') === true) {
				$column = $table->getColumn(name: 'description');
				$column->setLength(length: 20000);
			}
		}

		if($schema->hasTable(tableName: 'attachments') === true) {
			$table = $schema->getTable(tableName: 'attachments');
			if($table->hasColumn(name: 'description') === true) {
				$column = $table->getColumn(name: 'description');
				$column->setLength(length: 20000);
			}
		}

		if($schema->hasTable(tableName: 'catalogi') === true) {
			$table = $schema->getTable(tableName: 'catalogi');
			if($table->hasColumn(name: 'description') === true) {
				$column = $table->getColumn(name: 'description');
				$column->setLength(length: 20000);
			}
		}

		if($schema->hasTable(tableName: 'listings') === true) {
			$table = $schema->getTable(tableName: 'listings');
			if($table->hasColumn(name: 'description') === true) {
				$column = $table->getColumn(name: 'description');
				$column->setLength(length: 20000);
			}
		}

		if($schema->hasTable(tableName: 'metadata') === true) {
			$table = $schema->getTable(tableName: 'metadata');
			if($table->hasColumn(name: 'description') === true) {
				$column = $table->getColumn(name: 'description');
				$column->setLength(length: 20000);
			}
		}

		if($schema->hasTable(tableName: 'organisations') === true) {
			$table = $schema->getTable(tableName: 'organisations');
			if($table->hasColumn(name: 'description') === true) {
				$column = $table->getColumn(name: 'description');
				$column->setLength(length: 20000);
			}
		}

		if($schema->hasTable(tableName: 'themes') === true) {
			$table = $schema->getTable(tableName: 'themes');
			if($table->hasColumn(name: 'description') === true) {
				$column = $table->getColumn(name: 'description');
				$column->setLength(length: 20000);
			}
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
