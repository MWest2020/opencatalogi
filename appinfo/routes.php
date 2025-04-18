<?php

return [
	'routes' => [
		// Dashboard
		['name' => 'dashboard#index', 'url' => '/index', 'verb' => 'GET'],
		['name' => 'dashboard#page', 'url' => '/', 'verb' => 'GET'],
		// Directory
		['name' => 'listing#synchronise', 'url' => '/api/listings/synchronise/{id?}', 'verb' => 'POST'],
		['name' => 'directory#index', 'url' => '/api/directory', 'verb' => 'GET'],
		['name' => 'directory#show', 'url' => '/api/directory/{id}', 'verb' => 'GET', 'requirements' => ['path' => '.+']],
		['name' => 'directory#update', 'url' => '/api/directory', 'verb' => 'POST'],
		['name' => 'directory#publicationType', 'url' => '/api/directory/publication_types/{id}', 'verb' => 'GET'], // Should be in directory becouse its public
		// Catalogi
		['name' => 'catalogi#index', 'url' => '/api/catalogi/{id}', 'verb' => 'GET'],
		// Publications
		['name' => 'publications#index', 'url' => '/api/publications/{id}', 'verb' => 'GET'],
		['name' => 'publications#show', 'url' => '/api/publications/{id}/attachments', 'verb' => 'GET'],
		['name' => 'publications#attachments', 'url' => '/api/publications/{id}/attachments', 'verb' => 'GET'],
		['name' => 'publications#download', 'url' => '/api/publications/{id}/download', 'verb' => 'GET'],
		// Global Configuration
		['name' => 'settings#index', 'url' => '/api/settings', 'verb' => 'GET'],
		['name' => 'settings#create', 'url' => '/api/settings', 'verb' => 'POST'],

	]
];
