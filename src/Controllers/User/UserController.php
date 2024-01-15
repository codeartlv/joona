<?php

namespace Codeart\Joona\Controllers\User;

use Codeart\Joona\Contracts\Form;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Http\Request;
use Codeart\Joona\Facades\AdminAuth;
use Codeart\Joona\Helpers\FloodCheck;
use Codeart\Joona\Mail\UserPassword;
use Codeart\Joona\Models\User\Access\Role;
use Codeart\Joona\Models\User\AdminSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Codeart\Joona\Models\User\Log\LogEntry;

class UserController
{
	public function myProfile()
	{
		$user = AdminAuth::user();

		return view('joona::user.my-profile-form', [
			'first_name' => $user -> first_name,
			'last_name' => $user -> last_name,
			'email' => $user -> email,
		]);
	}

	public function saveMyProfile(Request $request)
	{
		$form = new Form();
		$user = AdminAuth::user();

		AdminUser::createOrUpdate([
			'first_name' => $request->post('first_name'),
			'last_name' => $request->post('last_name'),
			'email' => $request->post('email'),
		], $user, $form);

		if (!$form->hasError()) {
			$form->setSuccess(__('joona::user.profile_saved'));
			$form->setAction('close_popup', true);
		}

		return response()->json($form);
	}

	public function userList()
	{
		$chunk_size = 50;
		$user_chunk = AdminUser::with('roles')->paginate($chunk_size);
		$admin = AdminAuth::user();

		$users = $user_chunk->getCollection()->map(function ($user) use ($admin) {
			return $user->toArray() + [
				'can_manage' => $user->canBeManagedBy($admin),
			];
		})->toArray();

		return view('joona::user.list', [
			'total' => $user_chunk->total(),
			'size' => $chunk_size,
			'users' => $users,
		]);
	}

	public function setMyPasswordForm()
	{
		return view('joona::user.my-password');
	}

	public function setMyPassword(Request $request)
	{
		$form = new Form();
		$user = AdminAuth::user();

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

	public function editUser($user_id)
	{
		$admin = AdminAuth::user();
		$is_root = $admin->level == AdminUser::LEVEL_ADMIN;

		$fields = [
			'id' => 0,
			'level' => AdminUser::LEVEL_USER,
			'status' => AdminUser::STATUS_ACTIVE,
		];

		$user_roles = [];

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

		$levels = AdminUser::getLevels();
		$statuses = AdminUser::getStatuses();
		$available_levels = $admin->canManageLevels();
		$available_roles = $admin->canManageRoles();

		$roles = Role::orderBy('title', 'asc')->get()->map(function ($item) {
			return [
				'id' => (int) $item->id,
				'title' => $item -> title,
			];
		})->toArray();

		return view('joona::user.edit', [
			'fields' => $fields,
			'statuses' => $statuses,
			'levels' => $levels,
			'roles' => $roles,
			'user_roles' => $user_roles,
			'is_root' => $is_root,
			'available_levels' => $available_levels,
			'available_roles' => $available_roles,
		]);
	}

	public function saveUser(Request $request)
	{
		$form = new Form();
		$user = null;
		$admin = AdminAuth::user();

		$roles = (array) $request->post('roles');

		$level = $request->post('level');
		$password_setup = $request->post('password_setup');
		$user_id = $request->post('id');
		$available_roles = $admin->canManageRoles();

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

		if ($admin->level != AdminUser::LEVEL_ADMIN) {
			$level = AdminUser::LEVEL_USER;
		}

		$fields = [
			'first_name' => $request->post('first_name'),
			'last_name' => $request->post('last_name'),
			'email' => $request->post('email'),
			'level' => $level,
			'status' => $request->post('status'),
		];

		if (!$user_id) {
			$fields['username'] = $request->post('username');
		}

		if ($password_setup == 'set-password') {
			$fields['password'] = $request->post('password');
		}

		if ($password_setup == 'generate') {
			$fields['password'] = str_random_password(10);
		}

		AdminUser::createOrUpdate($fields, $user, $form);

		if (!$form->hasError()) {
			$form->setSuccess(__('joona::user.profile_saved'));
			$form->setAction('reset', true);
			$form->setAction('close_popup', true);
			$form->setAction('redirect', route('joona.user.list'));

			$user = $form->getData('user');

			if ($password_setup == 'generate') {
				Mail::to($user)->send(new UserPassword($user, $fields['password']));
			}

			$user->setRoles($roles);
		}

		return response()->json($form);
	}

	public function activityLog(Request $request)
	{
		$userid = (int) $request->get('user_id');
		$date_from = $request->get('date_from');
		$date_to = $request->get('date_to');
		$admin = AdminAuth::user();

		$can_display = false;
		$sessions = [];
		$users = AdminUser::orderBy('username', 'ASC')->get();

		if ($userid && $date_from && $date_to) {
			$d_from = \DateTime::createFromFormat('Y-m-d', $date_from);
			$d_to = \DateTime::createFromFormat('Y-m-d', $date_to);

			if ($d_to->getTimestamp() - $d_from->getTimestamp() > 0) {
				$can_display = true;

				$rows = AdminSession::whereRaw("user_id = ? AND started <= ? AND ended >= ? AND ended IS NOT NULL", [
					$userid,
					$d_to->format('Y-m-d 23:59:59'),
					$d_from->format('Y-m-d 00:00:00'),
				])->get();

				foreach ($rows as $row) {
					$duration = strtotime($row->ended) - strtotime($row->started);
					$duration_str = CarbonInterval::seconds($duration)->cascade()->forHumans();

					$started = Carbon::createFromTimestamp(strtotime($row->started));

					$logs = LogEntry::where('session_id', $row->id)->orderBy('id', 'asc')->get();
					$log_rows = [];

					foreach ($logs as $log) {
						$event = LogEntry::getEventFromCategory($log->action);

						$log_rows[] = [
							'date' => date('d.m.Y H:i:s', strtotime($log->created_at)),
							'action' => $event->getTitle(),
							'object' => $event->getDescription($log),
						];
					}

					$sessions[] = [
						'started' => $row->started,
						'started_date' => $started->toFormattedDateString(),
						'ended' => $row->ended,
						'duration_sec' => $duration,
						'duration_str' => $duration_str,
						'ip' => $row->login_ip,
						'end_reason' => __('joona::user.session_end_reason_' . $row->end_reason),
						'entries' => $log_rows,
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
}
