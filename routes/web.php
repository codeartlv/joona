<?php

use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Http\Controllers\Common\DashboardController;
use Codeart\Joona\Http\Controllers\Common\MiscController;
use Codeart\Joona\Http\Controllers\User\AuthController;
use Codeart\Joona\Http\Controllers\User\UserController;
use Codeart\Joona\Http\Controllers\User\PermissionsController;

/*
|--------------------------------------------------------------------------
| Admin panel routes
|--------------------------------------------------------------------------
*/

Route::prefix(Joona::getBasePath())->middleware(['web', 'admin.web'])->name('joona.')->group(function () {
	/**
	 * Unsecured routes
	 */

	// Authorization handling
	Route::prefix('/login')->name('user.')->group(function () {
		Route::get('/', [AuthController::class, 'loginForm'])->name('login');
		Route::post('/', [AuthController::class, 'authProcess'])->name('auth-process');
	});

	Route::get('/logout', [AuthController::class, 'logoutProcess'])->name('user.logout');

	// Misc routes
	Route::get('/set-locale/{locale}', [MiscController::class, 'setLocale'])->name('set-locale');
	Route::post('/set-theme/{mode}', [MiscController::class, 'setTheme'])->name('set-theme');

	/**
	 * Secured routes
	 */

	 Route::middleware(['admin.auth'])->group(function () {
		// User management
		Route::name('user.')->group(function () {
			Route::prefix('/users')->group(function () {
				Route::get('/', [UserController::class, 'userList'])->name('list');
				Route::get('/me', [UserController::class, 'myProfile'])->name('me');
				Route::post('/me', [UserController::class, 'saveMyProfile'])->name('me-save');
				Route::get('/my-password', [UserController::class, 'setMyPasswordForm'])->name('my-password');
				Route::post('/my-password', [UserController::class, 'setMyPassword'])->name('my-password-save');
				Route::get('edit/{id}', [UserController::class, 'editUser'])->name('edit');
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
	 });
});
