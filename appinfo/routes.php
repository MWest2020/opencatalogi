<?php

return [
	'resources' => [
		'metadata' => ['url' => '/api/metadata'],
		'publications' => ['url' => '/api/publications'],
		'organisations' => ['url' => '/api/organisations'],
		'themes' => ['url' => '/api/themes'],
		'attachments' => ['url' => '/api/attachments'],
		'catalogi' => ['url' => '/api/catalogi'],
		'directory' => ['url' => '/api/directory']
	],
	'routes' => [
		['name' => 'dashboard#page', 'url' => '/test', 'verb' => 'GET'],
		['name' => 'dashboard#page', 'url' => '/', 'verb' => 'GET'],
		//['name' => 'metadata#page', 'url' => '/metadata', 'verb' => 'GET'],
		//['name' => 'publications#page', 'url' => '/publications', 'verb' => 'GET'],
		//['name' => 'publications#attachments', 'url' => '/api/publications/{id}/attachments', 'verb' => 'GET'],
		//['name' => 'publications#attachmentsInternal', 'url' => '/api/publications/{id}/attachments/internal', 'verb' => 'GET'],
		//['name' => 'publications#download', 'url' => '/api/publications/{id}/download', 'verb' => 'GET'],
		//['name' => 'catalogi#page', 'url' => '/catalogi', 'verb' => 'GET'],
		//['name' => 'themes#indexInternal', 'url' => '/api/themes', 'verb' => 'GET'],
		//['name' => 'themes#showInternal', 'url' => '/api/themes/{id}', 'verb' => 'GET'],
		//['name' => 'directory#page', 'url' => '/directory', 'verb' => 'GET'],
		//['name' => 'directory#synchronise', 'url' => '/api/directory/{id}/sync', 'verb' => 'GET'],
		//['name' => 'configuration#index', 'url' => '/configuration', 'verb' => 'GET'],
		//['name' => 'configuration#create', 'url' => '/configuration', 'verb' => 'POST']
		// search endpoints		
		//['name' => 'search#preflighted_cors', 'url' => '/api/search/{path}', 'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		//['name' => 'search#index', 'url' => '/api/search', 'verb' => 'GET'],
		//['name' => 'search#show', 'url' => '/api/search/publication/{id}', 'verb' => 'GET', 'requirements' => ['id' => '\d+']],
		//['name' => 'search#show', 'url' => '/api/search/publication/{id}/attachments', 'verb' => 'GET', 'requirements' => ['id' => '\d+']],
		//['name' => 'themes#index', 'url' => '/api/search/themes', 'verb' => 'GET'],
		//['name' => 'themes#show', 'url' => '/api/search/themes/{id}', 'verb' => 'GET', 'requirements' => ['id' => '\d+']
	]
];
