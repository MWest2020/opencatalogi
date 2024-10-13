<?php

namespace OCA\OpenCatalogi\Controller;

use OCA\OpenCatalogi\Db\ListingMapper;
use OCA\OpenCatalogi\Service\DirectoryService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IAppConfig;
use OCP\IRequest;

class DirectoryController extends Controller
{
    public function __construct(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
		private readonly ListingMapper $listingMapper,
		private readonly DirectoryService $directoryService
	)
    {
        parent::__construct($appName, $request);
    }

	/**
	 * @PublicPage
	 * @NoCSRFRequired
	 */
	public function index(): JSONResponse
	{
		// Get all directories
        $data = $this->directoryService->getDirectories();
        
        // Return JSON response
        return new JSONResponse($data);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function show(string|int $id): JSONResponse
	{
		
	}

}
