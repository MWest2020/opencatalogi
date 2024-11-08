<?php

return [
	'resources' => [
		'publication_types' => ['url' => '/api/publication_types'],
		'publications' => ['url' => '/api/publications'],
		'organizations' => ['url' => '/api/organizations'],
		'themes' => ['url' => '/api/themes'],
		'attachments' => ['url' => '/api/attachments'],
		'catalogi' => ['url' => '/api/catalogi'],
		'listings' => ['url' => '/api/listings'],
	],
	'routes' => [
		// Directory
		['name' => 'listing#synchronise', 'url' => '/api/listings/synchronise/{id?}', 'verb' => 'POST'],
		['name' => 'directory#index', 'url' => '/api/directory', 'verb' => 'GET'],
		['name' => 'directory#show', 'url' => '/api/directory/{id}', 'verb' => 'GET', 'requirements' => ['path' => '.+']],
		['name' => 'directory#update', 'url' => '/api/directory', 'verb' => 'POST'],
		['name' => 'directory#publicationType', 'url' => '/api/directory/publication_types/{id}', 'verb' => 'GET'], // Should be in directory becouse its public
		// Publication
		['name' => 'publication_types#synchronise', 'url' => '/api/publication_types/synchronise', 'verb' => 'POST'],
		// Dashboard
		['name' => 'dashboard#index', 'url' => '/index', 'verb' => 'GET'],
		['name' => 'dashboard#page', 'url' => '/', 'verb' => 'GET'],
		// Publications
		['name' => 'publications#attachments', 'url' => '/api/publications/{id}/attachments', 'verb' => 'GET'],
		['name' => 'publications#download', 'url' => '/api/publications/{id}/download', 'verb' => 'GET'],
		// user Settings & Global Configuration
		['name' => 'settings#index', 'url' => '/settings', 'verb' => 'GET'],
		['name' => 'settings#create', 'url' => '/settings', 'verb' => 'POST'],
		['name' => 'configuration#index', 'url' => '/configuration', 'verb' => 'GET'],
		['name' => 'configuration#update', 'url' => '/configuration', 'verb' => 'PUT'],
		// Search
		['name' => 'search#preflighted_cors', 'url' => '/api/search/{path}', 'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		['name' => 'search#index', 'url' => '/api/search', 'verb' => 'GET'],
		['name' => 'search#publications', 'url' => '/api/search/publications', 'verb' => 'GET'],
		['name' => 'search#publication', 'url' => '/api/search/publications/{publicationId}', 'verb' => 'GET', 'requirements' => ['publicationId' => '[^/]+']],
		['name' => 'search#attachments', 'url' => '/api/search/publications/{publicationId}/attachments', 'verb' => 'GET', 'requirements' => ['publicationId' => '[^/]+']],
		['name' => 'search#themes', 'url' => '/api/search/themes', 'verb' => 'GET'],
		['name' => 'search#theme', 'url' => '/api/search/themes/{themeId}', 'verb' => 'GET', 'requirements' => ['themeId' => '\d+']]
	]
];
