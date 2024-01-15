<?php

use Codeart\Joona\Controllers\Common\DashboardController;
use Codeart\Joona\Controllers\Common\MiscController;
use Codeart\Joona\Controllers\User\AuthController;
use Codeart\Joona\Controllers\User\PermissionsController;
use Codeart\Joona\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('/admin')->middleware(['web', 'admin.web'])->name('joona.')->group(function () {
	// +-------------------------------------------------------------------
	// | Unsecured routes
	// +-------------------------------------------------------------------

	// Authorization handling
	Route::prefix('/login')->name('user.')->group(function () {
		Route::get('/', [AuthController::class, 'authForm'])->name('auth-form');
		Route::post('/', [AuthController::class, 'authProcess'])->name('auth-process');
	});

	Route::get('/set-locale/{locale}', [MiscController::class, 'setLocale'])->name('set-locale');
	Route::post('/set-theme/{mode}', [MiscController::class, 'setTheme'])->name('set-theme');
	Route::get('/data.js', [MiscController::class, 'getJsData'])->name('get-data');


	// +-------------------------------------------------------------------
	// | Secured routes
	// +-------------------------------------------------------------------
	Route::middleware(['admin.auth'])->group(function () {
		Route::prefix('/panel')->group(function () {
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
		});

		// Authorization handling
		Route::get('/logout', [AuthController::class, 'logoutProcess'])->name('user.auth-logout');

		// Dashboard
		Route::get('/', [DashboardController::class, 'dashboardIndex'])->name('dashboard');
	});
});
