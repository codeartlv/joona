<?php

namespace Codeart\Joona\View\Composers;

use Codeart\Joona\Facades\Auth;
use Codeart\Joona\Facades\Joona;
use \Illuminate\View\View;

class GlobalViewComposer
{
	public function compose(View $view): void
	{
		$currentUser = Auth::user();

		$view->with([
			'languages' => Joona::getLocaleList(),
			'theme' => session('theme'),
			'vite_resources' => Joona::getViteResources(),
			'email' => $currentUser ? $currentUser->email : '',
			'name' => $currentUser ? $currentUser->first_name.' '.$currentUser->last_name : '',
			'logo' => Joona::getLogo('light'),
			'logo_dark' => Joona::getLogo('dark'),
			'menu' => Joona::getNavigation(),
			'translations' => Joona::getJavascriptTranslations(),
		]);
	}
}
