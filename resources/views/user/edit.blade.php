<x-form :action="route('joona.user.save')">
	<input type="hidden" name="id" value="{{$fields['id']}}" />
	<x-dialog :caption="__($fields['id'] ? 'joona::user.edit_user_caption' : 'joona::user.create_user_caption')">

		<div x-data="adminUserEditForm">
			<div data-role="form.response"></div>

			<div class="form-group">
				<label class="required">@lang('joona::user.email')</label>
				<input class="form-control" type="email" maxlength="128" name="email" value="{{$fields['email'] ?? ''}}" autocomplete="email" />
			</div>

			<div class="block">
				<div class="form-split">
					<span>@lang('joona::user.edit_user_label_basic_data')</span>
				</div>

				<div class="block row">
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label class="required">@lang('joona::user.first_name')</label>
							<input class="form-control" type="text" maxlength="55" name="first_name" value="{{$fields['first_name'] ?? ''}}" autocomplete="name" />
						</div>
					</div>
					<div class="col-12 col-md-6">
						<div class="form-group">
							<label class="required">@lang('joona::user.last_name')</label>
							<input class="form-control" type="text" maxlength="55" name="last_name" value="{{$fields['last_name'] ?? ''}}" autocomplete="family-name" />
						</div>
					</div>
				</div>


			</div>

			<div class="block">
				<div class="form-split">
					<span>@lang('joona::user.edit_user_label_permissions')</span>
				</div>

				@if ($uses_permissions)
					<div class="form-group">
						<label class="required">@lang('joona::user.level')</label>
						<select class="form-select" name="level" data-role="level-toggle">
							@foreach ($available_levels as $level)
								<option {{$level->value == $fields['level'] ? 'selected':''}} value="{{$level->value}}">{{$level->getLabel()}}</option>
							@endforeach
						</select>
					</div>
				@endif

				<div class="form-group d-none mb-0" data-toggle="level" data-level="user">
					<label>@lang('joona::user.permissions.user_roles')</label>
					@if (!empty($roles))
						@foreach ($roles as $role)
							<div class="form-check">
								<input class="form-check-input" {{in_array($role['id'], $available_roles) ? '':'disabled'}} {{in_array($role['id'], $user_roles) ? 'checked':''}} name="roles[]" type="checkbox" value="{{$role['id']}}" id="role{{$role['id']}}">
								<label class="form-check-label" for="role{{$role['id']}}">
									{{$role['title']}}
								</label>
							</div>
						@endforeach
					@else
						<x-alert role="info" class="mb-0" :message="__('joona::user.no_roles_created_to_assign')" />
					@endif
				</div>
			</div>

			<div class="block">
				<div class="form-split">
					<span>@lang('joona::user.edit_user_label_password')</span>
				</div>

				<div class="block">
					@if ($fields['id'])
					<div class="form-check">
						<input class="form-check-input" type="radio" data-role="pass-setup" value="no-change" checked name="password_setup" id="pass_no_change">
						<label class="form-check-label" for="pass_no_change">
							@lang('joona::user.dont_change_pass')
						</label>
					</div>
					@endif

					<div class="form-check">
						<input class="form-check-input" type="radio" data-role="pass-setup" value="generate" name="password_setup" id="pass_email">
						<label class="form-check-label" for="pass_email">
							{{__($fields['id'] ? 'joona::user.generate_pass_new':'joona::user.generate_pass')}}
						</label>
					</div>
					<div class="form-check">
						<input class="form-check-input" type="radio" data-role="pass-setup" value="set-password" {{!$fields['id'] ? 'checked':''}}  name="password_setup" id="pass_set_manual">
						<label class="form-check-label" for="pass_set_manual">
							{{__($fields['id'] ? 'joona::user.change_pass':'joona::user.set_pass')}}
						</label>
					</div>
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
