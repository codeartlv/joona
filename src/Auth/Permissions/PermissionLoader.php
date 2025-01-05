<?php

namespace Codeart\Joona\Auth\Permissions;

use Codeart\Joona\Enums\UserLevel;
use Codeart\Joona\Facades\Joona;
use Illuminate\Support\Facades\Gate;
use Codeart\Joona\Models\User\AdminUser;
use InvalidArgumentException;

/**
 * Provides permission management and checking
 *
 * @package Codeart\Joona\Auth\Permissions
 */
class PermissionLoader
{
	/**
	 * List of permissions
	 *
	 * @var Permission[]
	 */
	private array $permissions = [];

	/**
	 * Instantiate permission handler
	 *
	 * @param array $permissions
	 * @return void
	 */
	public function __construct(array $permissions)
	{
		$this->addPermissions($permissions);
	}

	/**
	 * Add new permissions and register at the gate
	 *
	 * @param Permission[] $permissions
	 * @return void
	 */
	public function addPermissions(array $permissions)
	{
		$extracted = $this->extractPermissions($permissions);

		foreach ($extracted as $permission) {
			if (!$permission instanceof Permission) {
				continue;
			}

			$keys = $permission->getAccessKeys();

			foreach ($keys as $key) {
				Gate::define($key, function($user, ...$args) use ($permission, $key) {
					if (!$user instanceof AdminUser) {
						return false;
					}

					return $this->validate($user, $permission, $key, $args);
				});
			}
		}

		$this->permissions = array_merge($this->permissions, $permissions);
	}

	/**
	 * Return permissions without groups
	 *
	 * @param array $permissions
	 * @return array
	 */
	public function extractPermissions(array $permissions): array
	{
		$extracted = [];

		foreach ($permissions as $permission) {
			if ($permission instanceof PermissionGroup) {
				$extracted = array_merge($extracted, $permission->getItems());
				continue;
			}

			$extracted[] = $permission;
		}

		return $extracted;
	}

	/**
	 * Returns flattened permission keys
	 *
	 * @return array
	 */
	public function getPermissionKeys(): array
	{
		$keys = call_user_func_array('array_merge', array_map(
			fn ($permission) => $permission->getAccessKeys(),
			$this->extractPermissions($this->permissions)
		));

		return array_unique($keys);
	}

	/**
	 * Validates permission against the user
	 *
	 * @param AdminUser $user
	 * @param Permission $permission
	 * @param string $key
	 * @param mixed $args
	 * @return bool
	 * @throws InvalidArgumentException
	 */
	public function validate(AdminUser $user, Permission $permission, string $key, ...$args): bool
	{
		// Is functionality enabled globally
		if (!Joona::usesRolesAndPermissions()) {
			return true;
		}

		// Superusers are allowed always
		if (UserLevel::from($user->level) == UserLevel::Admin) {
			return true;
		}

		// Elevated permissions require superuser
		if ($permission->isElevated() && UserLevel::from($user->level) != UserLevel::Admin) {
			return false;
		}

		// If permission has callback, let it decide
		if (in_array(HasCallback::class, class_uses_recursive($permission))) {
			return (bool) call_user_func($permission->getCallback(), $user, ...$args);
		}

		// Check against defined permissions
		return count(array_intersect($permission->getAccessKeys(), $user->permissions())) > 0;
	}

	/**
	 * Checks whether permission key exists
	 *
	 * @param string $permissionKey
	 * @return bool
	 */
	public function isDefined(?string $permissionKey)
	{
		if (!$permissionKey) {
			return false;
		}

		return in_array($permissionKey, $this->getPermissionKeys());
	}

	/**
	 * Return defined groups and permissions
	 *
	 * @return Permission[]|PermissionGroup[]
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}
}
