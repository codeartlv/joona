<?php

namespace Codeart\Joona\Models\User\Access;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomPermission extends Pivot
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
	protected $table = 'admin_users_permissions';
}