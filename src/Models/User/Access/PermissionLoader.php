<?php

namespace Codeart\Joona\Models\User\Access;

use Codeart\Joona\Models\User\Access\Permissions\ConditionalPermission;
use Codeart\Joona\Models\User\Access\Permissions\GroupPermission;
use Codeart\Joona\Models\User\Access\Permissions\SimplePermission;
use Codeart\Joona\Models\User\AdminUser;

class PermissionLoader
{
	/**
	 * List of available permissions
	 * @var array
	 */
	protected $permissions = [];

	/**
	 * Routes that are elevated
	 * @var array
	 */
	protected $elevated_routes = [];

	public function __construct()
	{
		$this->loadPermissions();
	}

	/**
	 * Load permissions from config file
	 * @return void
	 */
	protected function loadPermissions()
	{
		$groups = config('permissions');

		if (isset($groups['elevated_routes'])) {
			$this->elevated_routes = $groups['elevated_routes'];
			unset($groups['elevated_routes']);
		}

		foreach ($groups as $group => $permissions) {
			foreach ($permissions as $permission_name => $data) {
				$permission_name = gettype($permission_name) == 'string' ? $permission_name : $data;
				$this->permissions[] = $this->createPermission($group, $permission_name, $data);
			}
		}
	}

	/**
	 * Create permission object
	 *
	 * @param mixed $group
	 * @param mixed $name
	 * @param mixed $data
	 * @return ConditionalPermission|GroupPermission|SimplePermission
	 */
	protected function createPermission($group, $name, $data)
	{
		if (is_callable($data)) {
			return new ConditionalPermission($group, $name, $data);
		} elseif (is_array($data)) {
			return new GroupPermission($group, $name, $data);
		}

		return new SimplePermission($group, $name);
	}

	/**
	 * Return list of permissions groupped by category
	 * @return array
	 */
	public function getGroupedPermissions()
	{
		$grouped = [];

		foreach ($this->permissions as $permission) {
			$grouped[$permission->getGroup()][] = $permission;
		}

		return $grouped;
	}

	/**
	 * Return list of all permissions
	 * @return array
	 */
	public function getPermissions()
	{
		return $this->permissions;
	}

	/**
	 * Checks whether permission exists
	 *
	 * @param string $permission_name
	 * @return bool
	 */
	public function isDefined(string $permission_name)
	{
		return in_array($permission_name, $this->getPlainPermissions()) || in_array($permission_name, $this->elevated_routes);
	}

	/**
	 * Return list of elevated permissions
	 *
	 * @return array
	 */
	public function getElevatedPermissions()
	{
		return $this->elevated_routes;
	}

	/**
	 * Return list of permissions
	 * @return array
	 */
	public function getPlainPermissions()
	{
		$result = [];

		foreach ($this->permissions as $permission) {
			$result[] = $permission->getId();

			if ($permission instanceof GroupPermission) {
				$result = array_merge($result, $permission->getCollection());
				continue;
			}
		}

		return $result;
	}

	/**
	 * Validate permission against user
	 *
	 * @param AdminUser $user
	 * @param string $permission_name
	 * @return bool
	 */
	public function validate(AdminUser $user, string $permission_name): bool
	{
		if (!config('joona.use_permissions')) {
			return true;
		}

		if ($user->level == AdminUser::LEVEL_ADMIN) {
			return true;
		}

		if (in_array($permission_name, $this->elevated_routes) && $user->level != AdminUser::LEVEL_ADMIN) {
			return false;
		}

		$available_perms = $user->permissions();

		foreach ($this->permissions as $permission) {
			$permission_id = $permission->getId();

			$is_simple_or_conditional = $permission instanceof SimplePermission || $permission instanceof ConditionalPermission;
			$is_group_permission = $permission instanceof GroupPermission && (in_array($permission_name, $permission->getCollection()) || $permission_id === $permission_name);

			if (($is_simple_or_conditional && $permission_id === $permission_name) || $is_group_permission) {
				return in_array($permission_id, $available_perms);
			}
		}

		return false;
	}
}
