<?php

namespace Codeart\Joona\Composers;

use Codeart\Joona\Facades\AdminAuth;
use \Illuminate\View\View;

class GlobalViewComposer
{
	public function compose(View $view): void
	{
		$current_user = AdminAuth::user();

		$view->with([
			'languages' => \get_admin_locales(),
			'theme' => session('theme'),
			'username' => $current_user ? $current_user->username : '',
			'menu' => \get_admin_menu(),
		]);
	}
}
