<x-form action="{{route('joona.user.save')}}">
	<input type="hidden" name="id" value="{{$fields['id']}}" />
	<x-dialog :caption="__($fields['id'] ? 'joona::user.edit_user_caption' : 'joona::user.create_user_caption')">
		<div data-bind="admin.userEditForm">
			<div class="form-group">
				<x-input :label="__('joona::user.email')" name="email" required="true" :value="$fields['email'] ?? ''" autocomplete="email" maxlength="128" type="email" />
			</div>

			<div class="block">
				<x-form-section-heading :label="__('joona::user.edit_user_label_basic_data')"/>

				<div class="block row">
					<div class="col-12 col-md-6">
						<div class="form-group">
							<x-input :label="__('joona::user.first_name')" required="true" name="first_name" :value="$fields['first_name'] ?? ''" autocomplete="name" maxlength="55" type="text" />
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<x-input :label="__('joona::user.last_name')" required="true" name="last_name" :value="$fields['last_name'] ?? ''" autocomplete="family-name" maxlength="55" type="text" />
						</div>
					</div>
				</div>
			</div>

			<div class="block">
				<x-form-section-heading :label="__('joona::user.edit_user_label_permissions')"/>

				@if ($uses_permissions)
					<div class="form-group">
						<x-select :label="__('joona::user.level')" required="true" name="level" data-role="level-toggle" :options="$available_levels" />
					</div>
				@endif

				<div class="form-group d-none mb-0" data-toggle="level" data-level="user">
					<label class="form-label">@lang('joona::user.permissions.user_roles')</label>
					@if (!empty($roles))
						@foreach ($roles as $role)
							<x-checkbox type="checkbox" :value="$role['id']" :checked="in_array($role['id'], $user_roles)" name="roles[]" :label="$role['title']"  />
						@endforeach
					@else
						<x-alert role="info" class="mb-0">{{__('joona::user.no_roles_created_to_assign')}}</x-alert>
					@endif
				</div>
			</div>

			@if (!empty($customPermissions))
				<div class="block">
					<x-form-section-heading :label="__('joona::user.edit_user_custom_permissions')"/>

					@foreach ($customPermissions as $group)
						<div class="form-group">
							<x-multiselect :label="$group['label']" name="permissions[]" :options="$group['permissions']" />
						</div>
					@endforeach
				</div>
			@endif

			<div class="block">
				<x-form-section-heading :label="__('joona::user.edit_user_label_password')"/>

				<div class="block">
					@if ($fields['id'])
						<x-checkbox type="radio" value="no-change" checked name="password_setup" data-role="pass-setup" :label="__('joona::user.dont_change_pass')" />
					@endif

					<x-checkbox type="radio" value="generate" name="password_setup" data-role="pass-setup" :checked="!$fields['id']" :label="__($fields['id'] ? 'joona::user.generate_pass_new':'joona::user.generate_pass')" />

					<x-checkbox type="radio" value="set-password" name="password_setup" data-role="pass-setup" :checked="!$fields['id']" :label="__($fields['id'] ? 'joona::user.change_pass':'joona::user.set_pass')" />
				</div>

				<div class="d-none form-group mb-0 block" data-role="password-input">
					<x-password-validator name="password" policy="{{config('joona.admin_password_policy')}}" />
				</div>
			</div>
		</div>

		<x-slot name="footer">
			<x-button :caption="__('joona::common.save')" icon="check" />
		</x-slot>
	</x-dialog>
</x-form>
