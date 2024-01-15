<?php

namespace Codeart\Joona\Models\User\Access\Permissions;

class GroupPermission extends Permission
{
	public function __construct(string $group, string $permission_id, protected array $collection)
	{
		parent::__construct($group, $permission_id);
	}

	/**
	 * Returns related collection
	 *
	 * @return array
	 */
	public function getCollection()
	{
		return $this->collection;
	}
}
