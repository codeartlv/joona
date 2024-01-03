<?php

namespace Codeart\Joona\Auth\Permissions;

/**
 * Permission for a specific route
 *
 * @package Codeart\Joona\Auth\Permissions
 */
class RoutePermission extends Permission
{
	/**
	 * Routes under permission
	 *
	 * @var array
	 */
	private $routes;

	public function __construct(string $id, string $label, bool $elevated = false, array $routes = [])
	{
		parent::__construct($id, $label, $elevated);

		$this->routes = $routes;
	}

	/**
	 * Return permission keys
	 *
	 * @return array
	 */
	protected function getKeys(): array
	{
		return $this->routes;
	}
}
