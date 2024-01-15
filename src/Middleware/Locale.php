<?php

namespace Codeart\Joona\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

class Locale
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$raw_locale = Session::get('locale');
		$admin_locales = get_admin_locales();
		$locale = Config::get('app.locale');

		if (isset($admin_locales[$raw_locale]) && $admin_locales[$raw_locale]['enabled']) {
			$locale = $raw_locale;
		}

		if (!isset($admin_locales[$locale])) {
			$locale = key($admin_locales);
		}

		App::setLocale($locale);

		$theme = Session::get('theme');

		if (!in_array($theme, ['dark', 'light'])) {
			$theme = 'light';
		}

		Session::put('theme', $theme);

		return $next($request);
	}
}
