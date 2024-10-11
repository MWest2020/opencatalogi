<?php

namespace OCA\OpenCatalogi\Service;

use Adbar\Dot;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Uid\Uuid;
use OCA\OpenCatalogi\Db\PublicationMapper;
use OCA\OpenCatalogi\Db\ThemeMapper;
use OCA\OpenCatalogi\Db\AttachmentMapper;
use OCA\OpenCatalogi\Db\OrganisationMapper;

class ObjectService
{
	/**
	 * Gets a guzzle client based upon given config.
	 *
	 * @param array $config The config to be used for the client.
	 * @return Client
	 */
	public function getObject(string $objectType, string $id, array $extend = []): Client
	{
		$guzzleConf = $config;
		unset($guzzleConf['mongodbCluster']);

		return new Client($config);
	}
	


}
