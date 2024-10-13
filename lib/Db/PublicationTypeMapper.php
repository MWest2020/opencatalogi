<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

class PublicationTypeMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_publication_types');
	}

	public function find($id, ?array $extend = []): PublicationType
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_publication_types')
			->where($qb->expr()->orX(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)),
				$qb->expr()->eq('uuid', $qb->createNamedParameter($id, IQueryBuilder::PARAM_STR))
			));

		$entity = $this->findEntity(query: $qb);
		
        if (!empty($extend)) {
			// todo: implement extending
		}

		return $entity;
	}

	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_publication_types')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	public function findAll(
		?int $limit = null, 
		?int $offset = null, 
		?array $filters = [], 
		?array $searchConditions = [], 
		?array $searchParams = [],
		?array $orderBy = [], 
		?array $extend = []
	): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_publication_types')
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

		$entities = $this->findEntities(query: $qb);

        if (!empty($extend)) {
			// todo: implement extending
		}

		return $entities ;
	}

	public function createFromArray(array $object): PublicationType
	{
		$publicationType = new PublicationType();
		$publicationType->hydrate(object: $object);

		// Set uuid if not provided
		if($publicationType->getUuid() === null){
			$publicationType->setUuid(Uuid::v4());
		}
		return $this->insert(entity: $publicationType);
	}

	public function updateFromArray(int $id, array $object): PublicationType
	{
		$publicationType = $this->find($id);
		$publicationType->hydrate($object);
		
		// Update the version
		$version = explode('.', $publicationType->getVersion());
		$version[2] = (int)$version[2] + 1;
		$publicationType->setVersion(implode('.', $version));

		return $this->update($publicationType);
	}
}
