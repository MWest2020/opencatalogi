<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class PublicationTypeMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_publicationType');
	}

	public function find(int $id): PublicationType
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_publicationType')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_publicationType')
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

	public function createFromArray(array $object): PublicationType
	{
		$publicationType = new PublicationType();
		$publicationType->hydrate(object: $object);
		return $this->insert(entity: $publicationType);
	}

	public function updateFromArray(int $id, array $object): PublicationType
	{
		$publicationType = $this->find($id);
		$publicationType->hydrate($object);

		return $this->update($publicationType);
	}
}
