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

	// Defines the admin password policy. Possible values, separated by commas:
	// min:<int> - minimum password length
	// max:<int> - length beyond which any password is considered secure
	// uppercase - requires at least one uppercase character
	// lowercase - requires at least one lowercase character
	// mixed - requires at least one lowercase and one uppercase character
	// number - requires at least one numeric character
	// special - requires at least one special character
	'admin_password_policy' => 'min:8,max:20,mixed,number,special',

	// Provide translations keys for use in Javascript trans() and choice()
	// functions.

	'js_translations' => [

	],

	// Provides option to block non-admin accounts when invalid password is
	// entered. The value signifies number of attempts. 0 means that this
	// option is disabled.
	'auto_block_user' => 0,
];
