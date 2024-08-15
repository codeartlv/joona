<?php

namespace Codeart\Joona\Http\Controllers\Common;

use Codeart\Joona\Facades\Joona;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class MiscController
{
	/**
	 * Sets the user's color theme preference in the session.
	 *
	 * If the provided theme is not in the list of available themes, it sets the
	 * theme to the default (the first theme in the list). This function updates
	 * the session with the user's theme preference.
	 *
	 * @param string $theme The color theme to be set.
	 * @return void
	 */
	public function setTheme(string $theme)
	{
		$themes = Joona::getColorThemes();

		if (!in_array($theme, $themes)) {
			$theme = reset($themes);
		}

		if ($theme !== false) {
			Session::put('theme', $theme);
		}
	}

	/**
	 * Sets the application's locale.
	 *
	 * If the provided locale is not in the list of available locales,
	 * the user is redirected to the Joona dashboard. Otherwise, the
	 * locale is set and the user is redirected back to the previous page.
	 *
	 * @param string $locale The locale to be set.
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function setLocale(string $locale)
	{
		$locales = Joona::getLocaleList();

		if (!isset($locales[$locale])) {
			return redirect() -> to(route('joona.dashboard'));
		}

		Session::put('locale', $locale);

		App::setLocale($locale);

		return redirect() -> back();
	}
}
