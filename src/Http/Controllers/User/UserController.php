<?php

namespace Codeart\Joona\Http\Controllers\User;

use Codeart\Joona\View\Components\Form\FormResponse;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Http\Request;
use Codeart\Joona\Facades\Auth;
use Codeart\Joona\Mail\UserPassword;
use Codeart\Joona\Models\User\Access\Role;
use Codeart\Joona\Models\User\AdminSession;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Codeart\Joona\Auth\Permissions\CustomPermission;
use Codeart\Joona\Auth\Permissions\PermissionGroup;
use Codeart\Joona\Enums\UserLevel;
use Codeart\Joona\Enums\UserStatus;
use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Facades\Permission;
use Codeart\Joona\Models\User\Log\LogEntry;
use Codeart\Joona\View\Components\Select\Option;
use Illuminate\Http\RedirectResponse;
use Jenssegers\Agent\Agent;

class UserController
{
	/**
	 * Displays the profile editing form for the current user.
	 *
	 * @return \Illuminate\View\View The view with the user's profile data.
	 */
	public function myProfile()
	{
		$user = Auth::user();

		return view('joona::user.my-profile-form', [
			'first_name' => $user -> first_name,
			'last_name' => $user -> last_name,
			'email' => $user -> email,
		]);
	}

	/**
	 * Saves the updated profile information for the current user.
	 *
	 * @param \Illuminate\Http\Request $request The request containing the profile data to be updated.
	 * @return \Illuminate\Http\JsonResponse The response indicating the result of the update operation.
	 */
	public function saveMyProfile(Request $request)
	{
		$form = new FormResponse();
		$user = Auth::user();

		AdminUser::createOrUpdate([
			'first_name' => $request->post('first_name'),
			'last_name' => $request->post('last_name'),
			'email' => $request->post('email'),
		], $user, $form);

		if (!$form->hasError()) {
			$form->setSuccess(__('joona::user.profile_saved'));
			$form->setAction('close_popup', true);
			$form->setAction('reload', true);
		}

		return response()->json($form);
	}

	/**
	 * Displays a paginated list of users with their management capabilities.
	 *
	 * @return \Illuminate\View\View The view with the user list and related data.
	 */
	public function userList()
	{
		$search = request()->query('search');
		$admin = Auth::user();
		$chunk_size = 50;
		$users = AdminUser::with('roles');
		
		if ($search) {
			$users
				->whereLike('email', '%'.$search.'%')
				->orWhereLike('first_name', '%'.$search.'%')
				->orWhereLike('last_name', '%'.$search.'%');
		}

		$user_chunk = $users->paginate($chunk_size);
		
		$users = $user_chunk->getCollection()->map(function ($user) use ($admin) {
			return $user->toArray() + [
				'can_manage' => $user->canBeManagedBy($admin),
				'status_label' => $user->status->getLabel(),
				'status_class' => $user->status->getClass(),
			];
		})->toArray();

		return view('joona::user.list', [
			'total' => $user_chunk->total(),
			'size' => $chunk_size,
			'users' => $users,
			'search' => $search,
			'uses_permissions' => Joona::usesRolesAndPermissions(),
		]);
	}

	/**
	 * Displays the user editing form for a specific user.
	 *
	 * @param int $user_id The ID of the user to edit.
	 * @return \Illuminate\View\View The view with the user's details for editing.
	 */
	public function editUser($user_id)
	{
		$admin = Auth::user();
		$is_root = UserLevel::from($admin->level) == UserLevel::Admin;

		$fields = [
			'id' => 0,
			'level' => UserLevel::Admin->value,
			'class' => '',
			'status' => UserStatus::ACTIVE,
		];

		$user_roles = [];
		$user = null;

		if ($user_id) {
			$user = AdminUser::find($user_id);

			if (!$user) {
				abort(403);
			}

			if (!$user->canBeManagedBy($admin)) {
				abort(403);
			}

			$fields = $user->toArray();
			$user_roles = $user->roles()->get()->pluck('id')->toArray();
		}

		$available_roles = $admin->canManageRoles();

		$roles = Role::orderBy('title', 'asc')->get()->map(function ($item) {
			return [
				'id' => (int) $item->id,
				'title' => $item -> title,
			];
		})->toArray();

		$levels = array_map(function ($level) use ($fields) {
			return new Option($level->value, $level->getLabel(), $level->value == $fields['level']);
		}, $admin->canManageLevels());

		$statuses = array_filter(array_map(function ($status) use ($fields, $user) {
			// When creating new user, onyl active should be available
			if (!$user && $status != UserStatus::ACTIVE) {
				return false;
			}

			// For existing users, filter out statuses that are not available
			if ($user && !$user->isStatusAvailable($status)) {
				return false;
			}

			return new Option($status->value, $status->getLabel(), $status->value == $fields['status']);
		}, UserStatus::cases()));

		// Fetch all permissions
		$permissions = Permission::getPermissions();
		$customPermissions = [];
		$ungrouppedPermissions = [];
		$userCustomPerm = $user ? $user->customPermissions->pluck('permission') : new Collection();

		foreach ($permissions as $permissionEntry) {
			if (!$permissionEntry instanceof PermissionGroup || $permissionEntry instanceof CustomPermission) {
				continue;
			}

			if ($permissionEntry instanceof CustomPermission) {
				$ungrouppedPermissions[] = new Option($permissionEntry->getId(), __($permissionEntry->getLabel()), $userCustomPerm->contains($permissionEntry->getId()));
			}

			if ($permissionEntry instanceof PermissionGroup) {
				$groupPermissions = [];

				foreach ($permissionEntry->getItems() as $permission) {
					if (!$permission instanceof CustomPermission) {
						continue;
					}

					$groupPermissions[] = new Option($permission->getId(), __($permission->getLabel()), $userCustomPerm->contains($permission->getId()));
				}

				if (!empty($groupPermissions)) {
					$customPermissions[] = [
						'label' => __($permissionEntry->getLabel()),
						'permissions' => $groupPermissions,
					];
				}
			}
		}

		if (!empty($ungrouppedPermissions)) {
			array_unshift($customPermissions, [
				'label' => __('joona::user.custom_permissions'),
				'permissions' => $ungrouppedPermissions,
			]);
		}

		if (!$is_root) {
			foreach ($roles as $i => $role) {
				if (!in_array($role['id'], $available_roles)) {
					unset($roles[$i]);
				}
			}
		}

		$classes = array_map(function($className) use ($fields){
			if ($className instanceof \UnitEnum) {
				return new Option($className, __('joona::user.class_'.$className->value), $className->value == $fields['class']);	
			}

			return new Option($className, __('joona::user.class_'.$className), $className == $fields['class']);
		}, Joona::getUserClasses());

		$classMode = config('joona.class_role_mode', 'interchangeable');
		
		return view('joona::user.edit', [
			'fields' => $fields,
			'roles' => $roles,
			'classes' => $classes,
			'user_roles' => $user_roles,
			'is_root' => $is_root,
			'statuses' => $statuses,
			'available_levels' => $levels,
			'available_roles' => $available_roles,
			'uses_permissions' => Joona::usesRolesAndPermissions(),
			'classMode' => $classMode,
			'customPermissions' => $customPermissions,
		]);
	}

	/**
	 * Delete user call
	 *
	 * @param string $userId
	 * @return RedirectResponse
	 */
	public function deleteUser(string $userId): RedirectResponse
	{
		$admin = Auth::user();
		$user = AdminUser::find($userId);

		if (!$user) {
			abort(404);
		}

		if (!$user->canBeManagedBy($admin)) {
			abort(403);
		}

		$user->delete();

		return redirect()->back();
	}

	/**
	 * Saves the user details from the edit form.
	 *
	 * @param \Illuminate\Http\Request $request The request containing the user details.
	 * @return \Illuminate\Http\JsonResponse The response indicating the result of the save operation.
	 */
	public function saveUser(Request $request)
	{
		$form = new FormResponse();
		$user = null;
		$admin = Auth::user();

		$roles = (array) $request->post('roles');

		$level = $request->post('level');
		$class = $request->post('class');
		$password_setup = $request->post('password_setup');
		$user_id = $request->post('id');
		$available_roles = $admin->canManageRoles();
		$permissions = (array) $request->post('permissions');
		$isCreateNew = (bool) !$user_id;

		$classMode = config('joona.class_role_mode', 'interchangeable');

		if (!Joona::usesRolesAndPermissions()) {
			$level = UserLevel::Admin->value;
		}
		
		if ($classMode == 'interchangeable' && $class) {
			$roles = [];
		}

		$roles = array_intersect($roles, $available_roles);

		if ($user_id) {
			$user = AdminUser::find($user_id);

			$user_roles = $user->roles()->pluck('id')->toArray();
			$disabled_roles = array_diff($user_roles, $available_roles);

			if (!empty($disabled_roles)) {
				$roles = array_merge($roles, $disabled_roles);
			}

			if (!$user) {
				$form->setError(__('joona::user.user_not_found'));
				return response()->json($form);
			}

			if (!$user->canBeManagedBy($admin)) {
				$form->setError(__('joona::common.access_denied'));
				return response()->json($form);
			}
		}

		if (UserLevel::from($admin->level) != UserLevel::Admin) {
			$level = UserLevel::User->value;
		}

		$classes = Joona::getUserClasses();

		foreach ($classes as $index => $className) {
			if ($className instanceof \UnitEnum) {
				$classes[$index] = $className->value;
			}
		}

		if ($class && !in_array($class, $classes)) {
			$form->setError(__('joona::user.class_not_found'), 'class');
			return response()->json($form);
		}

		$fields = [
			'first_name' => $request->post('first_name'),
			'last_name' => $request->post('last_name'),
			'email' => $request->post('email'),
			'level' => $level,
			'class' => $class,
			'status' => $request->post('status'),
		];

		if (!$user_id) {
			$fields['username'] = $request->post('username');
		}

		if ($password_setup == 'set-password') {
			$fields['password'] = $request->post('password');
		}

		if ($password_setup == 'generate') {
			$fields['password'] = Str::password(10);
		}

		AdminUser::createOrUpdate($fields, $user, $form);

		if (!$form->hasError()) {
			$form->setSuccess(__('joona::user.profile_saved'));
			$form->setAction('reset', true);
			$form->setAction('close_popup', true);
			$form->setAction('redirect', route('joona.user.list'));

			$user = $form->getData('user');

			if ($password_setup == 'generate') {
				Mail::to($user)->send(new UserPassword($user, $fields['password'], $isCreateNew));
			}

			$user->setRoles($roles);
			$user->setCustomPermissions($permissions);
		}

		return response()->json($form);
	}

	/**
	 * Display user event journal.
	 *
	 * @param \Illuminate\Http\Request $request The request containing the user details.
	 * @return \Illuminate\View\View The view corresponding to displaying results.
	 */
	public function activityLog(Request $request)
	{
		$userid = (int) $request->get('user_id');
		$date_from = $request->get('date_from');
		$date_to = $request->get('date_to');

		$can_display = false;
		$sessions = [];
		$users = AdminUser::orderBy('email', 'ASC')->get()->map(function ($user) use ($userid) {
			return new Option($user->id, $user->email, $userid == $user->id);
		})->all();

		if ($date_from && $date_to) {
			$d_from = \DateTime::createFromFormat('Y-m-d', $date_from);
			$d_to = \DateTime::createFromFormat('Y-m-d', $date_to);

			if ($d_to->getTimestamp() - $d_from->getTimestamp() > 0) {
				$can_display = true;

				$params = [];

				if ($userid) {
					$params[] = $userid;
				}

				$params[] = $d_to->format('Y-m-d 23:59:59');
				$params[] = $d_from->format('Y-m-d 00:00:00');

				$rows = AdminSession::whereRaw(($userid ? "user_id = ? AND":"")." started <= ? AND ended >= ? AND ended IS NOT NULL ORDER BY id ASC", $params)->get();

				foreach ($rows as $row) {
					$duration = strtotime($row->ended) - strtotime($row->started);
					$duration_str = CarbonInterval::seconds($duration)->cascade()->forHumans();

					$started = Carbon::createFromTimestamp(strtotime($row->started));

					$logs = LogEntry::where('session_id', $row->id)->orderBy('id', 'asc')->get();
					$log_rows = [];
					$agent = null;

					foreach ($logs as $log) {
						$event = LogEntry::getEventFromCategory($log->action);

						$log_rows[] = [
							'date' => date('d.m.Y H:i:s', strtotime($log->created_at)),
							'action' => $event->getTitle(),
							'object' => $event->getDescription($log),
						];

						if (!$agent) {
							$agent = $log->ua;
						}
					}

					$agentParser = new Agent();
					$agentParser->setUserAgent($agent);

					$browser = $agentParser->browser();
					$version = $agentParser->version($browser);

					$sessions[] = [
						'started' => $row->started,
						'started_date' => $started->toFormattedDateString(),
						'ended' => $row->ended,
						'duration_sec' => $duration,
						'duration_str' => $duration_str,
						'ip' => $row->login_ip,
						'end_reason' => __('joona::user.session_end_reason_' . $row->end_reason),
						'entries' => $log_rows,
						'user' => $row->user->toArray(),
						'agent' => [
							'platform' => $agentParser->platform(),
							'browser' => $agentParser->browser().' '.$version,
							'device' => $agentParser->device(),
						],
					];
				}
			}
		}

		return view('joona::user.activity_log', [
			'users' => $users,
			'user_id' => $userid,
			'date_from' => $date_from,
			'date_to' => $date_to,
			'can_display' => $can_display,
			'sessions' => $sessions,
			'total' => count($sessions),
		]);
	}

	/**
	 * Display form that allows password change for current user
	 *
	 * @return \Illuminate\View\View The view corresponding to password form.
	 */
	public function setMyPasswordForm()
	{
		return view('joona::user.my-password');
	}

	/**
	 * Saves the user password for current user
	 *
	 * @param \Illuminate\Http\Request $request The request containing the user data.
	 * @return \Illuminate\Http\JsonResponse The response indicating the result of the save operation.
	 */
	public function setMyPassword(Request $request)
	{
		$form = new FormResponse();
		$user = Auth::user();

		$new_password = $request->post('password');
		$current_password = $request->post('current_password');

		if (!Hash::check($current_password, $user->password)) {
			$form->setError(__('joona::validation.password.no_match'), 'current_password');
			return response()->json($form);
		}

		AdminUser::createOrUpdate([
			'password' => $new_password,
		], $user, $form);

		if (!$form->hasError()) {
			$form->setSuccess(__('joona::user.profile_saved'));
			$form->setAction('close_popup', true);
		}

		return response()->json($form);
	}
}
