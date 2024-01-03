<?php

namespace Codeart\Joona\Facades;

use Illuminate\Support\Facades\Facade;

class Permission extends Facade
{
	/**
	 * @inheritDoc
	 */
	protected static function getFacadeAccessor()
	{
		return 'joona.permission-loader';
	}
}
