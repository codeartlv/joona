<?php

namespace Codeart\Joona\Controllers\Common;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class MiscController
{
	public function getJsData()
	{
		$debugbar = app('debugbar');

		if ($debugbar) {
			$debugbar->disable();
		}

		header("Content-Type: text/javascript");

		$keywords = [
			'common.ok' => 'joona::common.ok',
			'common.delete' => 'joona::common.delete',
			'common.cancel' => 'joona::common.cancel',
			'common.error' => 'joona::common.error',
		];

		$translations = [];

		foreach ($keywords as $key => $translatable) {
			$translations[$key] = __($translatable);
		}

		$routes = [
			'joona.set-theme',
			'joona.user.me',
			'joona.user.my-password',
			'joona.user.edit',
			'joona.user.permission-edit-role',
		];

		$urls = [];

		foreach ($routes as $route_name) {
			$route = Route::getRoutes()->getByName($route_name);

			if ($route) {
				$urls[$route_name] = '/'.$route->uri();
			}
		}

		return json_encode([
			'routes' => $urls,
			'translations' => $translations,
		]);
	}

	public function setTheme(string $theme)
	{
		if (!in_array($theme, ['dark', 'light'])) {
			$theme = 'light';
		}

		Session::put('theme', $theme);
	}

	public function setLocale(string $locale)
	{
		$locales = get_admin_locales();

		if (!isset($locales[$locale])) {
			return redirect()->back();
		}

		$locale_data = $locales[$locale];

		if (!$locale_data['enabled']) {
			return redirect()->back();
		}

		session(['locale' => $locale]);
		App::setLocale($locale);

		return redirect() -> back();
	}
}
