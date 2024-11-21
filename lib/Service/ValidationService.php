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
use Symfony\Component\Uid\Uuid;

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

		if(Uuid::isValid($publicationTypeId)) {
			$publicationType->setUuid($publicationTypeId);
		}

		$validator = new Validator();
		$validator->setMaxErrors(100);

		if (empty($publicationType->getProperties()) === true) {
			return $publication;
		}


        // Check for default values, and only set the property if the property is empty
        foreach ($publicationType->getProperties() as $property) {
            if (isset($property['default']) === true && empty($property['default']) === false && (isset($publication['data'][$property['title']]) === false || empty($publication['data'][$property['title']]) === true)) {
                $publication['data'][$property['title']] = $property['default'];
            }
        }

		$result = $validator->validate(data: (object) json_decode(json_encode($publication['data'])), schema:  $publicationType->getSchema($this->urlGenerator));

		$publication['validation'] = [
            'errors' => [],
            'valid'  => true
        ];

		if ($result->hasError()) {
			$errors = (new ErrorFormatter())->format($result->error());
            foreach ($errors as $error) {
                $publication['validation']['errors'][] = $error[0];
            }
            $publication['validation']['valid']  = false;
		}

		return $publication;
	}

}
