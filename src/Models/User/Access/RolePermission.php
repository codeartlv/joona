<?php

namespace Codeart\Joona\Models\User\Access;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePermission extends Pivot
{
	/**
	 * Usage of timestamps
	 *
	 * @var false
	 */
	public $timestamps = false;

	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table = 'admin_permissions_role';
}
