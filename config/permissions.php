<?php

return [
	// Routes requiring only user level of "admin"
	'elevated_routes' => [
		'joona.user.permission-groups',
		'joona.user.permission-save-role',
		'joona.user.permission-edit-role',
		'joona.user.permission-delete-role',
		'joona.user.permission-save',
	],
	'admin_users' => [
		// List admin users
		'joona.user.list',

		// Modify admin users
		'manage_admin_users' => [
			'joona.user.edit',
			'joona.user.save'
		],

		'joona.user.activities',
	]
];
