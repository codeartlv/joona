<?php

namespace Codeart\Joona\Facades;

use Illuminate\Support\Facades\Facade;

class AdminAuth extends Facade
{
	/**
	 * @inheritDoc
	 */
	protected static function getFacadeAccessor()
	{
		return 'AdminAuth';
	}
}
