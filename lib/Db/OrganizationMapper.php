<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Organization;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

class OrganizationMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_organizations');
	}

	public function find($id): Organization
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_organizations')
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
			->from('ocat_organizations')
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
			->from('ocat_organizations')
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

	public function createFromArray(array $object): Organization
	{
		$organization = new Organization();
		$organization->hydrate(object: $object);

		// Set uuid if not provided
		if($organization->getUuid() === null){
			$organization->setUuid(Uuid::v4());
		}
		return $this->insert(entity: $organization);
	}

	public function updateFromArray(int $id, array $object): Organization
	{
		$organization = $this->find($id);
		$organization->hydrate($object);
		
		// Update the version
		$version = explode('.', $organization->getVersion());
		$version[2] = (int)$version[2] + 1;
		$organization->setVersion(implode('.', $version));

		return $this->update($organization);
	}
}
