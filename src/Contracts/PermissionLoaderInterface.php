<?php

namespace Codeart\Joona\Contracts;

use Codeart\Joona\Auth\Permissions\Permission;
use Codeart\Joona\Models\User\AdminUser;

/**
 * Requirement for permission loader
 *
 * @package Codeart\Joona\Contracts
 */
interface PermissionLoaderInterface
{
	public function validate(AdminUser $user, Permission $permission, string $key, ...$args): bool;
}
