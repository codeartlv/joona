<?php

namespace Codeart\Joona\Http\Controllers\User;

use Codeart\Joona\Auth\Permissions\PermissionGroup;
use Codeart\Joona\View\Components\Form\FormResponse;
use Codeart\Joona\Facades\AdminAuth;
use Codeart\Joona\Facades\Permission;
use Codeart\Joona\Models\User\Access\Role;
use Illuminate\Http\Request;

class PermissionsController
{
	/**
	 * Displays the permissions organized by groups for a given role.
	 *
	 * @param \Illuminate\Http\Request $request The incoming request, containing potentially a role ID.
	 * @return \Illuminate\View\View The view containing the permissions organized by groups.
	 */
	public function groups(Request $request)
	{
		$role_id = (int) $request->get('role_id');

		// Retrieve all roles, ordered alphabetically by title
		$roles = Role::orderBy('title', 'asc')->get();

		// Fetch all permissions
		$permissions = Permission::getPermissions();

		// Initialize the lists for grouped and ungrouped permissions
		$groupedPermissions = [];
		$ungroupedPermissions = [];

		foreach ($permissions as $permission) {
			// Check if the permission belongs to a group
			if ($permission instanceof PermissionGroup) {
				$groupPermissions = $this->getGroupPermissions($permission);

				// Add the group to the list if it contains permissions
				if (!empty($groupPermissions)) {
					$groupedPermissions[] = [
						'label' => __($permission->getLabel()),
						'permissions' => $groupPermissions,
					];
				}
			} else {
				// Handle ungrouped permissions
				$ungroupedPermissions[] = $this->formatPermission($permission);
			}
		}

		// Prepend ungrouped permissions if there are any
		if (!empty($ungroupedPermissions)) {
			array_unshift($groupedPermissions, [
				'label' => __('joona::user.ungrouped_permissions'),
				'permissions' => $ungroupedPermissions,
			]);
		}

		// Fetch selected permissions if a valid role is specified
		$selectedPermissions = $this->getSelectedPermissions($role_id);

		return view('joona::user.permissions', [
			'role_id' => $role_id,
			'roles' => $roles,
			'permissions' => $groupedPermissions,
			'selected' => $selectedPermissions,
		]);
	}

	/**
	 * Saves the permissions associated with a specific role.
	 *
	 * @param \Illuminate\Http\Request $request The incoming request containing the role ID and permissions.
	 * @return \Illuminate\Http\JsonResponse The response indicating success or failure.
	 */
	public function saveRoles(Request $request)
	{
		$role_id = (int) $request->post('role_id');
		$permissions = (array) $request->post('permissions');

		$form = new FormResponse();
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

	/**
	 * Creates or updates a role based on the provided data.
	 *
	 * @param \Illuminate\Http\Request $request The incoming request containing the role data.
	 * @return \Illuminate\Http\JsonResponse The response indicating the result of the save operation.
	 */
	public function saveRole(Request $request)
	{
		$role_id = (int) $request->post('id');
		$title = (string) $request->post('title');

		$form = new FormResponse();
		$role = null;

		if ($role_id) {
			$role = Role::find($role_id);
		}

		Role::createOrUpdate([
			'title' => $title,
		], $role, $form);

		if (!$form->hasError()) {
			$form->setSuccess(__('joona::user.role_saved'));
			$form->setAction('reset', true);
			$form->setAction('reload', true);
		}

		return response()->json($form);
	}

	/**
	 * Retrieves details for a specific role to be edited.
	 *
	 * @param int $role_id The ID of the role to be edited.
	 * @return \Illuminate\View\View The view for editing a role with the role data.
	 */
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

	/**
	 * Deletes a specific role identified by its ID.
	 *
	 * @param int $role_id The ID of the role to be deleted.
	 * @return \Illuminate\Http\RedirectResponse A redirect to the roles and permissions management page.
	 */
	public function deleteRole($role_id)
	{
		$role = Role::find($role_id);

		if ($role) {
			$role->delete();
		}

		return redirect(route('joona.user.permission-groups'));
	}

	/**
	 * Retrieves permissions associated with a specific group, excluding elevated permissions.
	 *
	 * @param PermissionGroup $group The permission group to process.
	 * @return array An array of permissions formatted for display.
	 */
	private function getGroupPermissions(PermissionGroup $group)
	{
		$permissions = [];

		foreach ($group->getItems() as $permission) {
			if (!$permission->isElevated()) {
				$permissions[] = $this->formatPermission($permission);
			}
		}

		return $permissions;
	}

	/**
	 * Formats a permission for display by extracting its ID and label.
	 *
	 * @param $permission The permission object to format.
	 * @return array The formatted permission.
	 */
	private function formatPermission($permission)
	{
		return [
			'id' => $permission->getId(),
			'label' => __($permission->getLabel()),
		];
	}

	/**
	 * Retrieves the selected permissions for a given role.
	 *
	 * @param int $role_id The ID of the role for which to fetch selected permissions.
	 * @return array An array of selected permission identifiers.
	 */
	private function getSelectedPermissions($role_id)
	{
		if ($role_id) {
			$role = Role::find($role_id);
			if ($role) {
				return $role->permissions()->get()->pluck('permission')->toArray();
			}
		}

		return [];
	}
}
