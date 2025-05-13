<?php

use Codeart\Joona\Http\Controllers\Common\MiscController;
use Codeart\Joona\Http\Controllers\User\AuthController;

/*
|--------------------------------------------------------------------------
| Admin panel routes
|--------------------------------------------------------------------------
*/

Route::name('joona.')->group(function () {
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

	// Invite routes
	Route::get('/invite/{hash}', [AuthController::class, 'inviteForm'])->name('user.invite');
	Route::post('/invite', [AuthController::class, 'inviteProcess'])->name('user.invite-process');

	// Forgot password
	Route::get('/recover', [AuthController::class, 'recoverForm'])->name('user.recover-form');
	Route::post('/recover', [AuthController::class, 'recoverStart'])->name('user.recover-start');
	Route::get('/recover/set', [AuthController::class, 'recoverSetForm'])->name('user.recover-set');
	Route::post('/recover/set', [AuthController::class, 'recoverFinish'])->name('user.recover-finish');
});
