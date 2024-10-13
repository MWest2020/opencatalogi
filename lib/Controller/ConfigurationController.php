<?php

namespace OCA\OpenCatalogi\Controller;

use OCP\IAppConfig;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class ConfigurationController extends Controller
{

	public function __construct(
		$appName,
		IAppConfig $config,
		IRequest $request
	) {
		parent::__construct($appName, $request);
		$this->config = $config;
		$this->request = $request;
	}

	
	/**
	 * Handling the post request
	 *
	 * @NoCSRFRequired
	 */
	public function index(): JSONResponse
	{
			return new JSONResponse([]);
	}

	/**
	 * Handling the post request
	 *
	 * @NoCSRFRequired
	 */
	public function update(): JSONResponse
	{
			return new JSONResponse([]);
	}
}
