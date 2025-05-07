<?php

return [
	'timezone' 							=> 'America/Argentina/Cordoba',
	'locale' 							=> 'es_ES',
	'language'							=> 'es',
	'db' 								=> [
		'driver' 		=> env('DB_DRIVER'),
		'host'			=> env('DB_HOST'),
		'username'		=> env('DB_USERNAME'),
		'password'		=> env('DB_PASSWORD'),
		'database'		=> env('DB_NAME'),
		'port'			=> env('DB_PORT'),
		'charset' 		=> 'utf8',
		'collation'		=> 'utf8_unicode_ci',
		'prefix' 		=> ''
	],
	'logger'							=> [
		'path' 		=> rootPath('/logs'),
		'level' 	=> \Monolog\Logger::DEBUG,
		'default'		=> [
			'name' 		=> 'app_log',
			'filename'	=> 'app_' . date('Y-m-d') . '.log'
		],
		'auction'		=> [
			'name' 		=> 'auction_log',
			'filename'	=> 'auction_' . date('Y-m-d') . '.log'
		],
		'cron'		=> [
			'name' 		=> 'cron_log',
			'filename'	=> 'cron_' . date('Y-m-d') . '.log'
		],
	],
	'public_path'						=> '/public',
	'assets_path'						=> '/assets',
	'storage_path'						=> '/storage',
	'images_path'						=> '/img/concursos/',
	'images_user_path'					=> '/img/users/',
	'images_cliente_path'				=> '/img/clientes/',
	'files_path'						=> '/pliegos/',
	'files_tmp'							=> '/tmp/',
	'templates_path'					=> '/resources/views/templates',
	'templates_cache_path'				=> '/resources/views/templates_c',
	// Slim Framework specifics
	'displayErrorDetails' 				=> false,
	'determineRouteBeforeAppMiddleware' => true
];