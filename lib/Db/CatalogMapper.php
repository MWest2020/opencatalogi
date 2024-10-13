<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Catalog;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

class CatalogMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_catalogi');
	}

	public function find($id): Catalog
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_catalogi')
			->where($qb->expr()->orX(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)),
				$qb->expr()->eq('uuid', $qb->createNamedParameter($id, IQueryBuilder::PARAM_STR))
			));

		return $this->findEntity(query: $qb);
	}

	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_catalogi')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_catalogi')
			->setMaxResults($limit)
			->setFirstResult($offset);

        foreach ($filters as $filter => $value) {
			if ($value === 'IS NOT NULL') {
				$qb->andWhere($qb->expr()->isNotNull($filter));
			} elseif ($value === 'IS NULL') {
				$qb->andWhere($qb->expr()->isNull($filter));
			} else {
				$qb->andWhere($qb->expr()->eq($filter, $qb->createNamedParameter($value)));
			}
        }

        if (!empty($searchConditions)) {
            $qb->andWhere('(' . implode(' OR ', $searchConditions) . ')');
            foreach ($searchParams as $param => $value) {
                $qb->setParameter($param, $value);
            }
        }

		return $this->findEntities(query: $qb);
	}

	public function createFromArray(array $object): Catalog
	{
		$catalog = new Catalog();
		$catalog->hydrate(object: $object);

		// Set uuid if not provided
		if($catalog->getUuid() === null){
			$catalog->setUuid(Uuid::v4());
		}

		return $this->insert(entity: $catalog);
	}

	public function updateFromArray(int $id, array $object): Catalog
	{
		$catalog = $this->find($id);
		$catalog->hydrate($object);		
		
		// Update the version
		$version = explode('.', $catalog->getVersion());
		$version[2] = (int)$version[2] + 1;
		$catalog->setVersion(implode('.', $version));

		return $this->update($catalog);
	}
}
