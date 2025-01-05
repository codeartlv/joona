<?php

namespace Codeart\Joona\Models\User;

use Codeart\Joona\Contracts\Result;
use Codeart\Joona\Enums\UserLevel;
use Codeart\Joona\Enums\UserStatus;
use Codeart\Joona\Models\User\Access\CustomPermission;
use Codeart\Joona\Models\User\Log\LogEntry;
use Codeart\Joona\Models\User\Log\LogEvent;
use Codeart\Joona\Models\User\Access\Role;
use Codeart\Joona\Models\User\Access\UserRole;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class AdminUser extends Authenticatable
{
	use SoftDeletes;

	/**
	 * Models associated table
	 *
	 * @var string
	 */
	protected $table = 'admin_users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'email',
		'password',
		'first_name',
		'last_name',
		'level',
		'class',
		'status',
		'failed_attempts',
		'logged_at',
		'logged_ip',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		'password',
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'password' => 'hashed',
			'failed_attempts' => 'int',
			'status' => UserStatus::class,
		];
	}

	public function logEvent(LogEvent $event, string $object_id = null, array $parameters = []): ?LogEntry
	{
		$session = AdminSession::getActiveSession($this);

		if (!$session) {
			return null;
		}

		$entry = new LogEntry([
			'user_id' => $this->id,
			'session_id' => $session->id,
			'ua' => request()->header('User-Agent'),
			'action' => LogEntry::getCategoryFromEvent($event),
			'category' => $event->getCategory(),
			'object_id' => $object_id,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
			'parameters' => json_encode($parameters),
		]);

		$entry->save();

		return $entry;
	}

	public static function createOrUpdate(array $attributes, AdminUser $user = null, Result $result = null)
	{
		$newStatus = null;
		$result =  $result ?: new Result();

		$rules = [

		];

		if (!$user || array_key_exists('first_name', $attributes)) {
			$rules['first_name'] = ['required', 'string', 'max:55'];
		}

		if (!$user || array_key_exists('level', $attributes)) {
			$rules['level'] = ['required', 'string'];
		}

		if (!$user || array_key_exists('last_name', $attributes)) {
			$rules['last_name'] = ['required', 'string', 'max:55'];
		}

		if (!$user || array_key_exists('status', $attributes)) {
			$rules['status'] = ['required', new Enum(UserStatus::class)];
		}

		if (!$user || array_key_exists('email', $attributes)) {
			$rules['email'] = ['required', 'string', 'email', 'max:128'];

			if ($user) {
				$rules['email'][] = Rule::unique('admin_users')->ignore($user->id)->whereNull('deleted_at');
			} else {
				$rules['email'][] = 'unique:admin_users,email,NULL,id,deleted_at,NULL';
			}

			$attributes['email'] = strtolower($attributes['email']);
		}

		if (!$user || array_key_exists('password', $attributes)) {
			$rules['password'] = ['required', 'string'];
		}

		$validator = Validator::make($attributes, $rules, [], [
			'email' => __('joona::user.email'),
			'password' => __('joona::user.password'),
			'first_name' => __('joona::user.first_name'),
			'last_name' => __('joona::user.last_name'),
			'status' => __('joona::user.status'),
		]);

		if ($validator->fails()) {
			$messages = $validator->errors()->messages();
			$result->setErrors($messages);
		}

		if (array_key_exists('status', $attributes)) {
			$status = UserStatus::from($attributes['status']);
			$newStatus = $status;

			if ($user && !$user->isStatusAvailable($status)) {
				$result->setError(__('joona::validation.enum.invalid'), 'status');
				return $result;
			}
		}

		if (isset($attributes['password']) && !self::isSecurePassword($attributes['password'])) {
			$result->setError(__('joona::validation.password.unsecure'), 'password');
		}

		if ($result->hasError()) {
			return $result;
		}

		if (!$user) {
			try {
				$user = self::create($attributes);
				$user->setPassword($attributes['password']);
			} catch (\Exception $e) {
				$result->setError(__('joona::common.failed_to_create'));
				return $result;
			}
		} else {
			if (isset($attributes['password'])) {
				$user->setPassword($attributes['password']);
			}

			if ($user->status == UserStatus::BLOCKED && $newStatus == UserStatus::ACTIVE) {
				$attributes['failed_attempts'] = 0;
			}

			$user->update($attributes);
		}

		$result->addData('user', $user);
		return $result;
	}

	public function roles()
	{
		return $this->belongsToMany(Role::class, 'admin_users_role')->using(UserRole::class);
	}

	public function customPermissions()
	{
		return $this->hasMany(CustomPermission::class, 'admin_user_id', 'id');
	}

	public function setRoles(array $roles)
	{
		$this->roles()->sync($roles);
	}

	public function setCustomPermissions(array $permissions)
	{
		$this->customPermissions()->delete();

		foreach ($permissions as $permission) {
			$this->customPermissions()->save(
				new CustomPermission([
					'admin_user_id' => $this->id,
					'permission' => $permission,
				])
			);
		}
	}

	public function permissions(): array
	{
		$permissions = collect();

		foreach ($this->roles as $role) {
			$permissions = $permissions->merge($role->permissions()->pluck('permission'));
		}

		$custom = $this->customPermissions->pluck('permission')->all();
		$permissions = $permissions->merge($custom);

		return $permissions->unique()->all();
	}

	public function canManageRoles(): array
	{
		$all_roles = Role::all()->pluck('id')->toArray();

		if (UserLevel::from($this->level) == UserLevel::Admin) {
			return $all_roles;
		}

		return $this->roles()->get()->pluck('id')->toArray();
	}

	public function canBeManagedBy(AdminUser $user): bool
	{
		if (UserLevel::from($user->level) == UserLevel::Admin) {
			// Admins can modify any user
			return true;
		} elseif (UserLevel::from($user->level) != UserLevel::Admin && UserLevel::from($this->level) != UserLevel::Admin) {
			// Non-admins can modify other non-admins
			return true;
		}

		return false;
	}

	public function canManageLevels(): array
	{
		if (UserLevel::from($this->level) == UserLevel::Admin) {
			return [UserLevel::Admin, UserLevel::User];
		}

		return [UserLevel::User];
	}

	/**
	 * Sets password for user
	 *
	 * @param string $password
	 * @return void
	 */
	public function setPassword($password, bool $force = false): bool
	{
		if ($this->isSecurePassword($password) || $force) {
			$this->password = Hash::make($password);
			return (bool) $this->save();
		}

		return false;
	}

	public static function isSecurePassword(string $password): bool
	{
		$policy = config('joona.admin_password_policy');
		$rules = explode(',', $policy);

		$regex_parts = [];
		$min_length = $max_length = null;

		foreach ($rules as $rule) {
			$parts = explode(':', $rule);
			$rule_name = $parts[0];
			$rule_value = $parts[1] ?? null;

			switch ($rule_name) {
				case 'min':
					$min_length = $rule_value;
					break;
				case 'max':
					$max_length = $rule_value;
					break;
				case 'mixed':
					$regex_parts[] = '(?=.*\p{Lu})(?=.*\p{Ll})';
					break;
				case 'uppercase':
					$regex_parts[] = '(?=.*\p{Lu})';
					break;
				case 'lowercase':
					$regex_parts[] = '(?=.*\p{Ll})';
					break;
				case 'number':
					$regex_parts[] = '(?=.*[0-9])';
					break;
				case 'special':
					$regex_parts[] = '(?=.*[^\p{L}\p{N}])';
					break;
			}
		}

		$password_length = mb_strlen($password, 'UTF-8');

		if (!$password_length) {
			return false;
		}

		// Check min length
		if (!is_null($min_length) && $password_length < $min_length) {
			return false;
		}

		// If the password exceeds max length, consider it secure
		if (!is_null($max_length) && $password_length > $max_length) {
			return true;
		}

		// Combine the regex parts and check the password
		if (!empty($regex_parts)) {
			$regex = '/' . implode('', $regex_parts) . '/u';
			return preg_match($regex, $password);
		}

		return true;
	}

	public function isRoot(): bool
	{
		return $this->level == UserLevel::Admin->value;
	}

	public function canAuthBeBlocked(): bool
	{
		return !$this->isRoot();
	}

	public function isStatusAvailable(UserStatus $status): bool
	{
		if (!$this->canAuthBeBlocked() && $status == UserStatus::BLOCKED) {
			return false;
		}

		return true;
	}
}
