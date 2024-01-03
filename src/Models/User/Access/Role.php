<?php

namespace Codeart\Joona\Models\User\Access;

use Codeart\Joona\Contracts\Result;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Role extends Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $table = 'admin_roles';

	/**
	 * Fillable properties
	 *
	 * @var string[]
	 */
	protected $fillable = [
		'title',
	];

	/**
	 * Casts
	 *
	 * @var string[]
	 */
	protected $casts = [
		'active' => 'bool',
		'level' => 'int',
	];

	/**
	 * Return list of users assigned to this role
	 *
	 * @return mixed
	 */
	public function users()
	{
		return $this->belongsToMany(AdminUser::class)->using(UserRole::class);
	}

	/**
	 * Returns list of permissions for the role
	 *
	 * @return mixed
	 */
	public function permissions()
	{
		return $this->hasMany(RolePermission::class, 'role_id');
	}

	/**
	 * Assigns permission to role
	 *
	 * @param string $permission
	 * @return void
	 */
	public function addPermission(string $permission)
	{
		$this->permissions()->create(['permission' => $permission]);
	}

	/**
	 * Creates or updates as role
	 *
	 * @param array $attributes
	 * @param Role|null $role
	 * @param Result|null $result
	 * @return Result|null
	 */
	public static function createOrUpdate(array $attributes, self $role = null, Result $result = null)
	{
		$result =  $result ?: new Result();

		if (!$role || array_key_exists('title', $attributes)) {
			$rules['title'] = ['required', 'string', 'max:55'];
		}

		$validator = Validator::make($attributes, $rules);

		if ($validator->fails()) {
			$messages = $validator->errors()->messages();
			$result->setErrors($messages);
		}

		if ($result->hasError()) {
			return $result;
		}

		if (!$role) {
			try {
				$role = self::create($attributes);
			} catch (\Exception $e) {
				$result->setError(__('joona::common.failed_to_create'));
				return $result;
			}
		} else {
			$role->update($attributes);
		}

		return $result;
	}
}
