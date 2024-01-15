<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Auth configuration
	|--------------------------------------------------------------------------
	|
	| This file adds additional auth guards and providers for use in backend
	| authorization.
	|
	*/

	'guards' => [
		'admin' => [
			'driver' => 'session',
			'provider' => 'joona',
		],
	],
	'providers' => [
		'joona' => [
			'driver' => 'eloquent',
			'model' => Codeart\Joona\Models\User\AdminUser::class,
		],
	]
];
