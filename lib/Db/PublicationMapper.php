<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\DB\Types;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

class PublicationMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_publications');
	}

	public function find(int $id): Publication
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_publications')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity($qb);
	}

	private function parseComplexFilter(IQueryBuilder $queryBuilder, array $filter, string $name): IQueryBuilder
	{
		foreach ($filter as $key => $value) {
			switch($key) {
				case '>=':
				case 'after':
					$queryBuilder->andWhere($queryBuilder->expr()->gte($name, $queryBuilder->createNamedParameter($value)));
					break;
				case '>':
				case 'strictly_after':
					$queryBuilder->andWhere($queryBuilder->expr()->gt($name, $queryBuilder->createNamedParameter($value)));
					break;
				case '<=':
				case 'before':
					$queryBuilder->andWhere($queryBuilder->expr()->lte($name, $queryBuilder->createNamedParameter($value)));
					break;
				case '<':
				case 'strictly_before':
					$queryBuilder->andWhere($queryBuilder->expr()->lt($name, $queryBuilder->createNamedParameter($value)));
					break;
				default:
					$queryBuilder->andWhere($queryBuilder->expr()->eq(x: $name, y: $queryBuilder->createNamedParameter($filter)));
			}
		}

		return $queryBuilder;
	}

	private function addFilters(IQueryBuilder $queryBuilder, array $filters): IQueryBuilder
	{
		foreach ($filters as $key => $filter) {
			if (is_array($filter) === false) {
				$queryBuilder->andWhere($queryBuilder->expr()->eq($key, $queryBuilder->createNamedParameter($filter)));
				$queryBuilder->setParameter($key, $filter);
				continue;
			}

			$queryBuilder = $this->parseComplexFilter(queryBuilder: $queryBuilder, filter: $filter, name: $key);
		}

		return $queryBuilder;
	}

	public function count(?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): int
	{


		$qb = $this->db->getQueryBuilder();

		$qb->selectAlias($qb->createFunction('COUNT(*)'), 'count')
			->from('ocat_publications');


		$qb = $this->addFilters(queryBuilder: $qb, filters: $filters);


		if (!empty($searchConditions)) {
			$qb->andWhere('(' . implode(' OR ', $searchConditions) . ')');
			foreach ($searchParams as $param => $value) {
				$qb->setParameter($param, $value);
			}
		}

		$cursor = $qb->execute();
		$row = $cursor->fetch();
		$cursor->closeCursor();

		return $row['count'];
	}

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = [], ?array $sort = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*',)
			->from('ocat_publications')
			->setMaxResults($limit)
			->setFirstResult($offset);

		$qb = $this->addFilters(queryBuilder: $qb, filters: $filters);

        // Add search conditions
        if (!empty($searchConditions)) {
            foreach ($searchConditions as $condition) {
                $qb->andWhere($condition);
            }

            // Bind all parameters at once using setParameters()
            $paramBindings = [];
            foreach ($searchParams as $param => $value) {
                // Handle catalogi parameters explicitly as integers
                if (strpos($param, ':catalogi_') === 0) {
                    $paramBindings[$param] = [$value, \PDO::PARAM_INT];
                } else {
                    // For all other parameters, bind normally
                    $paramBindings[$param] = $value;
                }
            }

            // Use setParameters to bind all at once
            foreach ($paramBindings as $param => $binding) {
                if (is_array($binding) === true) {
                    $qb->setParameter($param, $binding[0], $binding[1]);  // Bind with type
                } else {
                    $qb->setParameter($param, $binding);  // Bind normally
                }
            }
        }

		if (empty($sort) === false) {
			foreach ($sort as $field => $direction) {
				$direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
				$qb->addOrderBy($field, $direction);
			}
		}
		// Use the existing findEntities method to fetch and map the results
		return $this->findEntities($qb);
	}

	public function createFromArray(array $object): Publication
	{
		$publication = new Publication();
		$publication->hydrate(object: $object);

		// Set uuid if not provided
		if($obj->getUuid() === null){
			$obj->setUuid(Uuid::v4());
		}

		return $this->insert(entity: $publication);
	}

	public function updateFromArray(int $id, array $object): Publication
	{
		$publication = $this->find(id: $id);
		$publication->hydrate(object: $object);
		
		// Update the version
		$version = explode('.', $obj->getVersion());
		$version[2] = (int)$version[2] + 1;
		$obj->setVersion(implode('.', $version));

		return $this->update($publication);
	}
}
