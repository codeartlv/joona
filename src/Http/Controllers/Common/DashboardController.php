<?php

namespace Codeart\Joona\Http\Controllers\Common;

use Illuminate\View\View;

class DashboardController
{
	public function dashboardIndex()
	{
		return view('joona::common.dashboard');
	}

	public function menuList(): View
	{
		return view('joona::menu');
	}
}
