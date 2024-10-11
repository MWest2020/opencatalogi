<?php

namespace OCA\OpenCatalogi\Service;

use OCP\AppFramework\Http\JSONResponse;
use OCA\OpenCatalogi\Service\FileService;



class DownloadService
{

	public function __construct(
		private readonly FileService $fileService
	) {}


	/**
	 * Creates a pdf file containing all metadata of the given publication.
	 *
	 * @param ObjectService $objectService The ObjectService, used to connect to a MongoDB database.
	 * @param string|int $id The id of a Publication we want to create / update a pdf file for.
	 * @param array|null $options A few options for this function, "download" & "saveToNextCloud" can't be both false!
	 * "download" = If we should return a download response (true = default).
	 * "saveToNextCloud" = If we should create and save the file in NextCloud (true = default).
	 * "publication" = If we already have a publication body prevent extra database requests by passing it along.
	 *
	 * @return JSONResponse A JSONResponse for downloading the pdf file. Or a JSONResponse containing a downloadUrl for a nextCloud file. Or an error response.
	 * @throws LoaderError|RuntimeError|SyntaxError|MpdfException|Exception
	 */
	private function createPublicationFile(
		ObjectService $objectService, string|int $id,
		?array $options = [
			'download' => true,
			'saveToNextCloud' => true,
			'publication' => null
		]
	): JSONResponse
	{
		if ($options['download'] === false && $options['saveToNextCloud'] === false) {
			return new JSONResponse(data: ['error' => '$options "download" & "saveToNextCloud" for function
			createPublicationFile should not be both set to false'], statusCode: 500);
		}

		$publication = $options['publication'] ?? null;
		if ($publication === null) {
			$publication = $this->getPublicationData(id: $id, objectService: $objectService);
			if ($publication instanceof JSONResponse) {
				return $publication;
			}
		}

		// Create the PDF file using a twig template and publication data.
		$mpdf = $this->fileService->createPdf(twigTemplate: 'publication.html.twig', context: ['publication' => $publication]);

		// The filename.
		$filename = "{$publication['title']}.pdf";

		if (isset($options['saveToNextCloud']) === false || $options['saveToNextCloud'] === true) {
			// Output to a file.
			$mpdf->Output(name: $filename, dest: Destination::FILE);

			// Save the file in NextCloud.
			$shareLink = $this->saveFileToNextCloud(filename: $filename, publication: $publication);
			if ($shareLink instanceof JSONResponse) {
				return $shareLink;
			}
		}

		if (isset($options['download']) === false || $options['download'] === true) {
			// Output directly to the browser.
			$mpdf->Output(name: $filename, dest: Destination::DOWNLOAD);
		}

		// Remove tmp folder after mpdf->Output & $this->saveFileToNextCloud have been called.
		rmdir(directory: '/tmp/mpdf');

		if (isset($options['saveToNextCloud']) === false || $options['saveToNextCloud'] === true) {
			return new JSONResponse(data: [
					'downloadUrl' => "$shareLink/download",
					'filename' 	  => $filename
				], statusCode: 200
			);
		}
		return new JSONResponse([], 200);
	}


	/**
	 * Prepares the creation of a ZIP archive for a publication, by adding all folders & files we want in this zip
	 * to a $tempFolder that will be used as input for creating the actual ZIP archive later.
	 *
	 * @param string $tempFolder The tmp location used as input for creating the ZIP archive.
	 * @param array $attachments An array containing all Attachments (Bijlagen) for the Publication.
	 * @param array $publicationFile An array containing the downloadUrl and filename of the pdf file created that contains all metadata of the Publication.
	 *
	 * @return void
	 */
	private function prepareZip(string $tempFolder, array $attachments, array $publicationFile): void
	{
		// Create temporary directory
		if (file_exists(filename: $tempFolder) === false) {
			mkdir(directory: $tempFolder, recursive: true);
			if (count($attachments['results']) > 0) {
				mkdir(directory: "$tempFolder/Bijlagen", recursive: true);
			}
		}

		// Add .pdf file containing publication metadata.
		$file_content = file_get_contents(filename: $publicationFile['downloadUrl']);
		if ($file_content !== false) {
			file_put_contents(filename: "$tempFolder/{$publicationFile['filename']}", data: $file_content);
		}

		// Add all attachments in Bijlagen folder.
		foreach ($attachments['results'] as $attachment) {
			$attachment = $attachment->jsonSerialize();
			$file_content = file_get_contents(filename: $attachment['downloadUrl']);
			if ($file_content !== false) {
				$filePath = explode(separator: '/', string: $attachment['reference']);
				file_put_contents(filename: "$tempFolder/Bijlagen/".end(array: $filePath), data: $file_content);
			}
		}
	}


	/**
	 * Creates a ZIP archive containing a pdf file with all metadata of the publication for id = $id.
	 * Will also add all Attachments (Bijlagen) of this publication to this ZIP archive in a folder called 'Bijlagen'.
	 *
	 * @param ObjectService $objectService The ObjectService, used to connect to a MongoDB database.
	 * @param string|int $id The id of a Publication we want to download a ZIP archive for.
	 *
	 * @return JSONResponse A JSONResponse for downloading the ZIP archive. Or an error response.
	 * @throws LoaderError|MpdfException|RuntimeError|SyntaxError
	 */
	private function createPublicationZip(ObjectService $objectService, string|int $id): JSONResponse
	{
		// Get the publication.
		$publication = $this->getPublicationData(id: $id, objectService: $objectService);
		if ($publication instanceof JSONResponse) {
			return $publication;
		}

		// Update the publication .pdf file containing publication metadata.
		$jsonResponse = $this->createPublicationFile(objectService: $objectService, id: $id,
			options: ['download' => false, 'publication' => $publication]);
		if ($jsonResponse->getStatus() !== 200) {
			return $jsonResponse;
		}
		$publicationFile = $jsonResponse->getData();

		// Get all publication attachments.
		$attachments = $this->attachments(id: $id, objectService: $objectService, publication: $publication)->getData();
		if (isset($attachments['results']) === false) {
			return new JSONResponse(data: ['error' => "failed to get attachments for this publication: $id"], statusCode: 500);
		}

		// Temporary paths.
		$tempFolder = '/tmp/nextcloud_download_' . $publication['title'];
		$tempZip = '/tmp/publicatie_' . $publication['title'] . '.zip';

		// Prepare ZIP by creating a temp folder with everything we want in the ZIP archive.
		$this->prepareZip(tempFolder: $tempFolder, attachments: $attachments, publicationFile: $publicationFile);

		// Create the ZIP archive.
		$error = $this->fileService->createZip(inputFolder: $tempFolder, tempZip: $tempZip);
		if ($error !== null) {
			return new JSONResponse(data: ['error' => "failed to create ZIP archive for this publication: $id"], statusCode: 500);
		}

		// Return a download response containing the ZIP archive. And clean up temp files/folders.
		$this->fileService->downloadZip(tempZip: $tempZip, inputFolder: $tempFolder);

		return new JSONResponse([], 200);
	}

}
