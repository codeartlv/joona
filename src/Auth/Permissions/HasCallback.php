<?php

namespace Codeart\Joona\Auth\Permissions;

use Closure;

/**
 * Binds object to provide a callback
 *
 * @package Codeart\Joona\Auth\Permissions
 */
interface HasCallback
{
	/**
	 * Returns callback function
	 *
	 * @return Closure
	 */
	public function getCallback(): Closure;
}
