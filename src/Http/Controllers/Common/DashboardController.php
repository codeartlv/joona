<?php

namespace Codeart\Joona\Http\Controllers\Common;

class DashboardController
{
	public function dashboardIndex()
	{
		return view('joona::common.dashboard');
	}
}
