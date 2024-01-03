<?php

namespace Codeart\Joona\Auth\Permissions;

use Closure;

/**
 * Simple permission
 *
 * @package Codeart\Joona\Auth\Permissions
 */
class SimplePermission extends Permission implements HasCallback
{
	/**
	 * Permission callback
	 * @var null|Closure
	 */
	private $callback;

	public function __construct(string $id, string $label, bool $elevated = false, ?Closure $callback = null)
	{
		parent::__construct($id, $label, $elevated);

		$this->callback = $callback;
	}

	/**
	 * Returns permission callback
	 *
	 * @return Closure
	 */
	public function getCallback(): Closure
	{
		return $this->callback;
	}

	/**
	 * Return permission keys
	 *
	 * @return array
	 */
	protected function getKeys(): array
	{
		return [
			$this->id
		];
	}
}
