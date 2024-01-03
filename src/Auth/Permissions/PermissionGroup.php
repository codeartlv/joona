<?php

namespace Codeart\Joona\Auth\Permissions;

use Closure;

/**
 * Groups permissions
 *
 * @package Codeart\Joona\Auth\Permissions
 */
class PermissionGroup
{
	/**
	 * Group label
	 *
	 * @var null|string
	 */
	private ?string $label;

	/**
	 * Group items
	 *
	 * @var array
	 */
	private $permissions = [];

	private function __construct(string $label, array $permissions)
	{
		$this->permissions = $permissions;
		$this->label = $label;
	}

	/**
	 * Create new permission group
	 *
	 * @param mixed $label
	 * @param array $permissions
	 * @return PermissionGroup
	 */
	public static function make($label, array $permissions)
	{
		return new self($label, $permissions);
	}

	/**
	 * Returns permission items in collection
	 *
	 * @return array
	 */
	public function getItems(): array
	{
		return $this->permissions;
	}

	/**
	 * Returns group label
	 *
	 * @return string
	 */
	public function getLabel(): string
	{
		return $this->label;
	}
}
