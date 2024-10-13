<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Listing;
use OCA\OpenCatalogi\Db\Organization;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

class ListingMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_listings');
	}

	public function find($id): Listing
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_listings')
			->where($qb->expr()->orX(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)),
				$qb->expr()->eq('uuid', $qb->createNamedParameter($id, IQueryBuilder::PARAM_STR))
			));

		return $this->findEntityCustom(query: $qb);
	}

	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_listings')
			->where($qb->expr()->orX(
				$qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)),
				$qb->expr()->in('uuid', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_STR_ARRAY))
			));

		return $this->findEntities(query: $qb);
	}

	/**
	 * Returns a db result and throws exceptions when there are more or less
	 * results CUSTOM FOR JOINS
	 *
	 * @param IQueryBuilder $query
	 * @return Entity the entity
	 * @psalm-return T the entity
	 * @throws Exception
	 * @throws MultipleObjectsReturnedException if more than one item exist
	 * @throws DoesNotExistException if the item does not exist
	 * @since 14.0.0
	 */
	protected function findEntityCustom(IQueryBuilder $query): Entity {
		return $this->mapRowToEntityCustom($this->findOneQuery($query));
	}

    /**
     *  CUSTOM FOR JOINS
     */
    protected function mapRowToEntityCustom(array $row): Entity {
		unset($row['DOCTRINE_ROWNUM']); // remove doctrine/dbal helper column

        // Map the Organization fields to a sub-array
        $organizationData = [
            'id' => $row['organization_id'] ?? null,
            'title' => $row['organization_title'] ?? null,
            'summary' => $row['organization_summary'] ?? null,
            'description' => $row['organization_description'] ?? null,
            'image' => $row['organization_image'] ?? null,
            'oin' => $row['organization_oin'] ?? null,
            'tooi' => $row['organization_tooi'] ?? null,
            'rsin' => $row['organization_rsin'] ?? null,
            'pki' => $row['organization_pki'] ?? null,
        ];

        $organizationIsEmpty = true;
        foreach ($organizationData as $key => $value) {
            if ($value !== null) {
                $organizationIsEmpty = false;
            }

            if (array_key_exists("organization_$key", $row) === true) {
                unset($row["organization_$key"]);
            }
        }

        $row['organization'] = $organizationIsEmpty === true ? null : json_encode(Organization::fromRow($organizationData)->jsonSerialize());

		return \call_user_func($this->entityClass .'::fromRow', $row);
	}

	/**
	 * Runs a sql query and returns an array of entities CUSTOM FOR JOINS
	 *
	 * @param IQueryBuilder $query
	 * @return Entity[] all fetched entities
	 * @psalm-return T[] all fetched entities
	 * @throws Exception
	 * @since 14.0.0
	 */
	protected function findEntitiesCustom(IQueryBuilder $query): array {
		$result = $query->executeQuery();
		try {
			$entities = [];
			while ($row = $result->fetch()) {
				$entities[] = $this->mapRowToEntityCustom($row);
			}
			return $entities;
		} finally {
			$result->closeCursor();
		}
	}

    public function findAll(?int $limit = null, ?int $offset = null, ?array $filters = [], ?array $searchConditions = [], ?array $searchParams = []): array
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from('ocat_listings')
            ->setMaxResults($limit)
            ->setFirstResult($offset);


        // Apply filters
        foreach ($filters as $filter => $value) {
            if ($value === 'IS NOT NULL') {
                $qb->andWhere($qb->expr()->isNotNull($filter));
            } elseif ($value === 'IS NULL') {
                $qb->andWhere($qb->expr()->isNull($filter));
            } else {
                $qb->andWhere($qb->expr()->eq($filter, $qb->createNamedParameter($value)));
            }
        }

        // Apply search conditions
        if (!empty($searchConditions)) {
            $qb->andWhere('(' . implode(' OR ', $searchConditions) . ')');
            foreach ($searchParams as $param => $value) {
                $qb->setParameter($param, $value);
            }
        }

        // Use the existing findEntities method to fetch and map the results
        return $this->findEntitiesCustom($qb);
    }
	/**
	 * Find a Listing by its catalog ID and directory
	 *
	 * @param string $catalogId The catalog ID to search for, should be a uuid
	 * @param string $directory The directory to search for, should be a url	
	 * @return Listing|null The found Listing entity or null if not found
	 */
	public function findByCatalogIdAndDirectory(string $catalogId, string $directory): ?Listing
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_listings')
			->where($qb->expr()->eq('uuid', $qb->createNamedParameter($catalogId)))
			->andWhere($qb->expr()->eq('directory', $qb->createNamedParameter($directory)))
			->setMaxResults(1);

		try {
			return $this->findEntity($qb);
		} catch (\OCP\AppFramework\Db\DoesNotExistException $e) {
			return null;
		}
	}

	public function createFromArray(array $object): Listing
	{
		$listing = new Listing();
		$listing->hydrate(object: $object);

		// Set uuid if not provided
		if($listing->getUuid() === null){
			$listing->setUuid(Uuid::v4());
		}

		return $this->insert(entity: $listing);
	}

	public function updateFromArray(int $id, array $object): Listing
	{
		$listing = $this->find($id);
		$listing->hydrate($object);
		
		// Update the version
		$version = explode('.', $listing->getVersion());
		$version[2] = (int)$version[2] + 1;
		$listing->setVersion(implode('.', $version));

		return $this->update($listing);
	}
}
