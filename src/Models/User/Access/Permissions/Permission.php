<?php

namespace Codeart\Joona\Models\User\Access\Permissions;

abstract class Permission
{
	public function __construct(protected string $group, protected string $permission_id)
	{
	}

	/**
	 * Return permission group name
	 * @return string
	 */
	public function getGroup(): string
	{
		return $this->group;
	}

	/**
	 * Return permission ID
	 * @return string
	 */
	public function getId(): string
	{
		return $this->permission_id;
	}

	/**
	 * Return readable permission name
	 * @return mixed
	 */
	public function getTitle()
	{
		return __('joona::permissions.'.$this->getId());
	}
}
