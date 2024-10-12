<?php

namespace OCA\OpenCatalogi\Db;

use OCA\OpenCatalogi\Db\Publication;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use Symfony\Component\Uid\Uuid;

class AttachmentMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, tableName: 'ocat_attachments');
	}

	public function find(int $id): Attachment
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_attachments')
			->where(
				$qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT))
			);

		return $this->findEntity(query: $qb);
	}

	public function findMultiple(array $ids): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_attachments')
			->where($qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)));

		return $this->findEntities(query: $qb);
	}

	public function findAll($limit = null, $offset = null): array
	{
		$qb = $this->db->getQueryBuilder();

		$qb->select('*')
			->from('ocat_attachments')
			->setMaxResults($limit)
			->setFirstResult($offset);

		return $this->findEntities(query: $qb);
	}

	public function createFromArray(array $object): Attachment
	{
		$attachment = new Attachment();
		$attachment->hydrate(object: $object);

		// Set uuid if not provided
		if($obj->getUuid() === null){
			$obj->setUuid(Uuid::v4());
		}

		return $this->insert(entity: $attachment);
	}

	public function updateFromArray(int $id, array $object): Attachment
	{
		$attachment = $this->find($id);
		$attachment->hydrate($object);		
		
		// Update the version
		$version = explode('.', $obj->getVersion());
		$version[2] = (int)$version[2] + 1;
		$obj->setVersion(implode('.', $version));

		return $this->update($attachment);
	}
}
