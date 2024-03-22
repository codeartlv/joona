<?php

namespace App\Providers;

use Codeart\Joona\MetaData\Locale;
use Codeart\Joona\MetaData\Page;
use Codeart\Joona\Panel;
use Codeart\Joona\Providers\JoonaPanelProvider;

class JoonaServiceProvider extends JoonaPanelProvider
{
	protected function configure(Panel $panel): void
	{
		$panel
			->setLocales([
				new Locale('English', 'en', 'us'),
			])
			->addViteResources([
				'resources/css/app.scss',
				'resources/js/app.js'
			])
			->addPages([

			])
			->addPermissions([

			]);

		return;
	}
}
