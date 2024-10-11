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
		['name' => 'dashboard#index', 'url' => '/index', 'verb' => 'GET'],
		['name' => 'dashboard#page', 'url' => '/', 'verb' => 'GET'],
		//['name' => 'publications#attachments', 'url' => '/api/publications/{id}/attachments', 'verb' => 'GET'],
		//['name' => 'publications#attachmentsInternal', 'url' => '/api/publications/{id}/attachments/internal', 'verb' => 'GET'],
		//['name' => 'publications#download', 'url' => '/api/publications/{id}/download', 'verb' => 'GET'],
		//['name' => 'directory#synchronise', 'url' => '/api/directory/{id}/sync', 'verb' => 'GET'],
		//['name' => 'settings#index', 'url' => '/configuration', 'verb' => 'GET'],
		//['name' => 'settings#create', 'url' => '/configuration', 'verb' => 'POST']
		// Search (external) endpoints		
		['name' => 'search#preflighted_cors', 'url' => '/api/search/{path}', 'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		['name' => 'search#publications', 'url' => '/api/search', 'verb' => 'GET'],
		['name' => 'search#publication', 'url' => '/api/search/publication/{publicationId}', 'verb' => 'GET', 'requirements' => ['publicationId' => '\d+']],
		['name' => 'search#attachments', 'url' => '/api/search/publication/{publicationId}/attachments', 'verb' => 'GET', 'requirements' => ['publicationId' => '\d+']],
		['name' => 'search#theme', 'url' => '/api/search/themes', 'verb' => 'GET'],
		['name' => 'search#themes', 'url' => '/api/search/themes/{themeId}', 'verb' => 'GET', 'requirements' => ['themeId' => '\d+']]
	]
];
