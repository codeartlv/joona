<?php

namespace Codeart\Joona\View\Composers;

use Codeart\Joona\Facades\Joona;
use \Illuminate\View\View;

class MailComposer
{
	public function compose(View $view): void
	{
		$view->with([
			'admin_url' => Joona::getUrl(),
		]);
	} 
}
