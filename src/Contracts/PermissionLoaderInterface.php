<?php

namespace Codeart\Joona\Contracts;

use Codeart\Joona\Auth\Permissions\Permission;

/**
 * Requirement for permission loader
 *
 * @package Codeart\Joona\Contracts
 */
interface PermissionLoaderInterface
{
	public function validate(Permission $permission, string $key, ...$args): bool;
}
