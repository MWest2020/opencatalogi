<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\IAppConfig;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCA\OpenCatalogi\Service\ObjectService;

class SettingsController extends Controller
{

	private ObjectService $objectService;
	
	public function __construct(
		$appName,
		IAppConfig $config,
		IRequest $request,
		ObjectService $objectService
	) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->request = $request;
		$this->objectService = $objectService;	
	}

	/**
	 * @NoCSRFRequired
	 */
	public function index(): JSONResponse
	{
		// Initialize the data array
		$data = [];
		$data['objectTypes'] = ['attachment', 'catalog', 'listing', 'metadata', 'organisation', 'publication', 'theme'];
		$data['openRegisters'] = false;
		$data['availableRegisters'] = [];
		
		// Check if the OpenRegister service is available
		$openRegisters = $this->objectService->getOpenRegisters();
		if($openRegisters !== null) {
			$data['openRegisters'] = true;
			$data['availableRegisters'] = $openRegisters->getRegisters();
		}	

		// Get the default values for the object types
		$defaults = [
			'attachment_source' => 'internal',
			'attachment_schema' => '',
			'attachment_register' => '',
			'catalog_source' => 'internal',
			'catalog_schema' => '',
			'catalog_register' => '',
			'listing_source' => 'internal',
			'listing_schema' => '',
			'listing_register' => '',
			'metadata_source' => 'internal',
			'metadata_schema' => '',
			'metadata_register' => '',
			'organisation_source' => 'internal',
			'organisation_schema' => '',
			'organisation_register' => '',
			'publication_source' => 'internal',
			'publication_schema' => '',
			'publication_register' => '',
			'theme_source' => 'internal',
			'theme_schema' => '',
			'theme_register' => ''
		];

		// Get the current values for the object types from the configuration
		try {
			foreach ($defaults as $key => $value) {
				$data[$key] = $this->config->getValueString($this->appName, $key, $value);
			}
			return new JSONResponse($data);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], 500);
		}
	}

	/**
	 * Handling the post request
	 *
	 * @NoCSRFRequired
	 */
	public function create(): JSONResponse
	{
		$data = $this->request->getParams();

		try {
			foreach ($data as $key => $value) {
				$this->config->setValueString($this->appName, $key, $value);
				$data[$key] = $this->config->getValueString($this->appName, $key);
			}
			return new JSONResponse($data);
		} catch (\Exception $e) {
			return new JSONResponse(['error' => $e->getMessage()], 500);
		}
	}
}
