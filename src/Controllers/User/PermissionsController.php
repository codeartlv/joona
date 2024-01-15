<?php

namespace Codeart\Joona\Controllers\User;

use Codeart\Joona\Contracts\Form;
use Codeart\Joona\Facades\AdminAuth;
use Codeart\Joona\Facades\Permission;
use Codeart\Joona\Models\User\Access\Role;
use Illuminate\Http\Request;

class PermissionsController
{
	public function groups(Request $request)
	{
		$role_id = (int) $request->get('role_id');

		$roles = Role::orderBy('title', 'asc')->get()->toArray();

		$permissions = Permission::getGroupedPermissions();
		$selected_permissions = [];

		if ($role_id) {
			$role = Role::find($role_id);

			if ($role) {
				$selected_permissions = $role->permissions()->get()->pluck('permission')->toArray();
			}
		}

		return view('joona::user.permissions', [
			'role_id' => $role_id,
			'roles' => $roles,
			'permissions' => $permissions,
			'selected' => $selected_permissions,
		]);
	}

	public function saveRoles(Request $request)
	{
		$role_id = (int) $request->post('role_id');
		$permissions = (array) $request->post('permissions');

		$form = new Form();
		$role = Role::find($role_id);

		if (!$role) {
			$form->setError(__('joona::user.permissions.role_not_found'));
			return response()->json($form);
		}

		$role->permissions()->delete();

		foreach ($permissions as $permission_id) {
			$role->addPermission($permission_id);
		}

		$form->setSuccess(__('joona::user.permissions.role_permissions_saved'));
		return response()->json($form);
	}

	public function saveRole(Request $request)
	{
		$role_id = (int) $request->post('id');
		$title = (string) $request->post('title');

		$form = new Form();
		$role = null;

		if ($role_id) {
			$role = Role::find($role_id);
		}

		Role::createOrUpdate([
			'title' => $title,
		], $role, $form);

		if (!$form->hasError()) {
			$form->setSuccess(__('joona::user.profile_saved'));
			$form->setAction('reset', true);
			$form->setAction('reload', true);
		}

		return response()->json($form);
	}

	public function editRole($role_id)
	{
		$data = [
			'id' => 0,
			'title' => '',
		];

		if ($role_id) {
			$role = Role::find($role_id);

			if ($role) {
				$data = $role->toArray();
			}
		}

		return view('joona::user.permission-role-edit', $data);
	}

	public function deleteRole($role_id)
	{
		$role = Role::find($role_id);

		if ($role) {
			$role->delete();
		}

		return redirect(route('joona.user.permission-groups'));
	}
}
