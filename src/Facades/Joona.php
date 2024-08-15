<?php

namespace Codeart\Joona\Facades;

use Illuminate\Support\Facades\Facade;

class Joona extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'joona.panel';
	}
}
