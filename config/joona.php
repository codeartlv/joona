<?php

return [
	/*
	|--------------------------------------------------------------------------
	| Joona Configuration
	|--------------------------------------------------------------------------
	|
	| This file provides the default configuration values for the Joona backend
	| template.
	|
	*/

	// Logo used in the admin panel.
	'app_logo' => env('APP_LOGO', '/vendor/joona/images/example_logo.png'),

	// Lists available locales for the backend.
	'locales' => [
		'lv' => [
			'title' => 'LAT',
			'enabled' => true,
		],
		'en' => [
			'title' => 'ENG',
			'enabled' => true,
		],
		'ru' => [
			'title' => 'RUS',
			'enabled' => true,
		],
	],

	// Map country codes to locales where they don't match.
	// Format: <locale> => <country code>
	'locale_map' => [
		'en' => 'us',
		'et' => 'ee',
	],

	// Use embedded permission and role mechanism
	'use_permissions' => true,

	// Defines the admin password policy. Possible values, separated by commas:
	// min:<int> - minimum password length
	// max:<int> - length beyond which any password is considered secure
	// uppercase - requires at least one uppercase character
	// lowercase - requires at least one lowercase character
	// mixed - requires at least one lowercase and one uppercase character
	// number - requires at least one numeric character
	// special - requires at least one special character
	'admin_password_policy' => 'min:8,max:20,mixed,number,special',

	// Define the backend menu with up to 2 levels of nesting.
	// The menu is automatically filtered based on available permissions.
	// Format:
	//
	// 'menu' => [
	//     'blog' => [
	//         'caption' => 'Blog',
	//         'icon' => 'article',
	//         'route' => 'blog.index',
	//         'active' => [],
	//         'childs' => [] // Submenu in the same format as parent
	//     ],
	// ];
	//
	// The 'settings' key is predefined in the package for appending items.
	//
	// 'menu' => [
	//     'settings' => [
	//         'childs' => [
	//             [ ... your custom entries ... ]
	//         ],
	//     ],
	// ];
	//
	// The "Dashboard" entry is under the key 'home'.
	// Active route is automatically matched, and the menu item is marked
	// as active. For multiple routes matching as active, list them in an array under the 'active' key.
	//
	'menu' => [

	]
];
