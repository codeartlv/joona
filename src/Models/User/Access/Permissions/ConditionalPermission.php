<?php

namespace Codeart\Joona\Models\User\Access\Permissions;

use Codeart\Joona\Models\User\AdminUser;

class ConditionalPermission extends Permission
{
	/**
	 * Callback function
	 *
	 * @var callable
	 */
	protected $callback;

	public function __construct(string $group, string $permission_id, callable $callback)
	{
		parent::__construct($group, $permission_id);
		$this->callback = $callback;
	}

	/**
	 * Check the permission
	 *
	 * @param AdminUser $user
	 * @param mixed $args
	 * @return mixed
	 */
	public function check(AdminUser $user, ...$args)
	{
		return call_user_func($this->callback, $user, ...$args);
	}
}
