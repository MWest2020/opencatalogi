<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Theme;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

class ThemeMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_themes');
	}

	public function find(int $id): Theme
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_themes')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_themes')
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

	public function createFromArray(array $object): Theme
	{
		$theme = new Theme();
		$theme->hydrate(object: $object);

		// Set uuid if not provided
		if($theme->getUuid() === null){
			$theme->setUuid(Uuid::v4());
		}
		return $this->insert(entity: $theme);
	}

	public function updateFromArray(int $id, array $object): Theme
	{
		$theme = $this->find($id);
		$theme->hydrate($object);
		
		// Update the version
		$version = explode('.', $theme->getVersion());
		$version[2] = (int)$version[2] + 1;
		$theme->setVersion(implode('.', $version));

		return $this->update($theme);
	}
}
