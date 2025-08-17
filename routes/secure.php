<?php

use Codeart\Joona\Http\Controllers\Common\ComponentsController;
use Codeart\Joona\Http\Controllers\Common\DashboardController;
use Codeart\Joona\Http\Controllers\User\UserController;
use Codeart\Joona\Http\Controllers\User\PermissionsController;

/*
|--------------------------------------------------------------------------
| Admin panel routes
|--------------------------------------------------------------------------
*/

Route::name('joona.')->group(function () {
	// User management
	Route::name('user.')->group(function () {
		Route::prefix('/users')->group(function () {
			Route::get('/', [UserController::class, 'userList'])->name('list');
			Route::get('/me', [UserController::class, 'myProfile'])->name('me');
			Route::post('/me', [UserController::class, 'saveMyProfile'])->name('me-save');
			Route::get('/my-password', [UserController::class, 'setMyPasswordForm'])->name('my-password');
			Route::post('/my-password', [UserController::class, 'setMyPassword'])->name('my-password-save');
			Route::get('edit/{id}', [UserController::class, 'editUser'])->name('edit');
			Route::get('delete/{user_id}', [UserController::class, 'deleteUser'])->name('delete');
			Route::post('edit', [UserController::class, 'saveUser'])->name('save');

			Route::get('/permissions', [PermissionsController::class, 'groups'])->name('permission-groups');
			Route::get('/permissions/edit-role/{id}', [PermissionsController::class, 'editRole'])->name('permission-edit-role');
			Route::post('/permissions/save-role', [PermissionsController::class, 'saveRole'])->name('permission-save-role');
			Route::get('/permissions/delete-role/{id}', [PermissionsController::class, 'deleteRole'])->name('permission-delete-role');
			Route::post('/permissions/save', [PermissionsController::class, 'saveRoles'])->name('permission-save');
			Route::get('/permissions/manage/{user_id}', [PermissionsController::class, 'editUserPermissions'])->name('permission-user-edit');
			Route::post('/permissions/manage', [PermissionsController::class, 'saveUserPermissions'])->name('permission-user-save');

			Route::get('/activites', [UserController::class, 'activityLog'])->name('activities');
		});
	});

	// Dashboard
	Route::get('/', [DashboardController::class, 'dashboardIndex'])->name('dashboard');

	// Component routes
	Route::get('/crop', [ComponentsController::class, 'crop'])->name('cropper');
});
