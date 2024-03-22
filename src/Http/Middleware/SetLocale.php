<?php

namespace Codeart\Joona\Http\Middleware;

use Closure;
use Codeart\Joona\Facades\Joona;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;

class SetLocale
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
		$admin_locales = Joona::getLocales();

		$locale_code = Config::get('app.locale');

		if (isset($admin_locales[$raw_locale])) {
			$locale_code = $raw_locale;
		}

		if (!isset($admin_locales[$locale_code]) && !empty($admin_locales)) {
			$locale_code = key($admin_locales);
		}

		App::setLocale($locale_code);

		$theme = Session::get('theme');
		$themes = Joona::getColorThemes();

		if (!in_array($theme, $themes)) {
			$theme = reset($themes);
		}

		Session::put('theme', $theme);

		return $next($request);
	}
}
