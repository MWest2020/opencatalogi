<?php

namespace OCA\OpenCatalogi\Service;

use OCA\OpenCatalogi\Db\CatalogMapper;
use OCA\OpenCatalogi\Db\Publication;
use OCA\OpenCatalogi\Db\PublicationType;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\OCS\OCSBadRequestException;
use OCP\AppFramework\OCS\OCSNotFoundException;
use OCP\IAppConfig;
use OCP\IURLGenerator;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ValidationService
 *
 * This service handles validation of catalogs and publications.
 */
class ValidationService
{

	public function __construct(
		private readonly ObjectService $objectService,
		private readonly IURLGenerator $urlGenerator,
	)
	{
	}

	/**
	 * Validate a publication to the definition defined in the PublicationType.
	 *
	 * @param Publication $publication The publication to validate.
	 * @return Publication The validated publication.
	 *
	 * @throws DoesNotExistException
	 * @throws MultipleObjectsReturnedException
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function validatePublication(array $publication): array
	{
		$publicationTypeId = $publication['publicationType'];
		$publicationType   = $this->objectService->getObject(objectType: 'publicationType', id: $publicationTypeId);

		$publicationType = (new PublicationType())->hydrate($publicationType);

		$validator = new Validator();
		$validator->setMaxErrors(100);

		if(empty($publicationType->getProperties()) === true) {
			return $publication;
		}

		$result = $validator->validate(data: (object) json_decode(json_encode($publication['data'])), schema:  $publicationType->getSchema($this->urlGenerator));

		$publication['validation'] = [];

		if ($result->hasError()) {
			$errors = (new ErrorFormatter())->format($result->error());
			$publication['validation'] = $errors;
		}

		return $publication;
	}

}
