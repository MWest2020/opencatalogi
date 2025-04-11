<?php

namespace OCA\OpenCatalogi\Controller;

ini_set('memory_limit', '2048M');

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\StreamResponse;
use OCP\IAppConfig;
use OCP\IRequest;
/**
 * Class ImagesController
 *
 * This controller handles images in the OpenCatalogi app.
 */
class ImagesController extends Controller
{
    /**
     * ImagesController constructor.
     *
     * @param string $appName The name of the app
     * @param IRequest $request The request object
     * @param IAppConfig $config The app configuration
     */
    public function __construct
	(
		$appName,
		IRequest $request,
		private readonly IAppConfig $config,
	)
    {
        parent::__construct($appName, $request);
    }

	/**
	 * Fetch image from given url.
	 *
	 * @return StreamResponse response containing the fetched image.
	 *
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function proxy(): StreamResponse
    {
        // Get all parameters from the request
        $data = $this->request->getParams();
        if (isset($data['url']) === false && filter_var($data['url'], FILTER_VALIDATE_URL) !== false) {
            return new JSONResponse(['message' => 'No url or invalid url given'], 400);
        }
        $url = $data['url'];
    
        // Regular proxying logic for URL images
        $image = file_get_contents($url);
        if ($image === false) {
            return new JSONResponse(['message' => 'Unable to fetch image'], 500);
        }
    
        // Determine content type from the headers (optional)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($image);
    
        $response = new StreamResponse(fopen('data://image/'.$mimeType.';base64,' . base64_encode($image), 'rb'));
        $response->addHeader('Content-Type', $mimeType ?? 'image/jpeg');
        $response->addHeader('Content-Length', strlen($image));
        $response->addHeader('Cache-Control', 'public, max-age=86400');

        return $response;
    }
}
