<x-joona-form :action="route('joona.user.save')">
	<input type="hidden" name="id" value="{{$fields['id']}}" />
	<x-joona-dialog :caption="__($fields['id'] ? 'joona::user.edit_user_caption' : 'joona::user.create_user_caption')">

		<div data-bind="admin.user-edit-form">
			<div data-role="form.response"></div>

			<div class="block">
				<div class="form-split">
					<span>@lang('joona::user.edit_user_label_basic_data')</span>
				</div>

				<x-joona-form-group required="true" :label="__('joona::user.username')">
					<input class="form-control" name="username" maxlength="25" type="text" {{$fields['id'] ? 'disabled':''}} value="{{$fields['username'] ?? ''}}" />
				</x-joona-form-group>

				<div class="row">
					<div class="col-12 col-md-6">
						<x-joona-form-group required="true" :label="__('joona::user.first_name')">
							<input class="form-control" type="text" maxlength="55" name="first_name" value="{{$fields['first_name'] ?? ''}}" autocomplete="name" />
						</x-joona-form-group>
					</div>
					<div class="col-12 col-md-6">
						<x-joona-form-group required="true" :label="__('joona::user.last_name')">
							<input class="form-control" type="text" maxlength="55" name="last_name" value="{{$fields['last_name'] ?? ''}}" autocomplete="family-name" />
						</x-joona-form-group>
					</div>
				</div>

				<x-joona-form-group required="true" :label="__('joona::user.email')">
					<input class="form-control" type="email" maxlength="128" name="email" value="{{$fields['email'] ?? ''}}" autocomplete="email" />
				</x-joona-form-group>

				<div class="form-split">
					<span>@lang('joona::user.edit_user_label_permissions')</span>
				</div>

				<x-joona-form-group required="true" :label="__('joona::user.level')">
					<select class="form-select" name="level" data-role="level-toggle">
						@foreach ($levels as $level)
							@if (in_array($level['value'], $available_levels))
							<option {{$level['value'] == $fields['level'] ? 'selected':''}} value="{{$level['value']}}">{{$level['caption']}}</option>
							@endif
						@endforeach
					</select>
				</x-joona-form-group>

				<div class="form-group d-none" data-toggle="level" data-level="user">
					<label>@lang('joona::user.permissions.user_roles')</label>
					@foreach ($roles as $role)
						<div class="form-check">
							<input class="form-check-input" {{in_array($role['id'], $available_roles) ? '':'disabled'}} {{in_array($role['id'], $user_roles) ? 'checked':''}} name="roles[]" type="checkbox" value="{{$role['id']}}" id="role{{$role['id']}}">
							<label class="form-check-label" for="role{{$role['id']}}">
								{{$role['title']}}
							</label>
						</div>
					@endforeach
				</div>

				<x-joona-form-group required="true" :label="__('joona::user.status')">
					<select class="form-select" name="status">
						@foreach ($statuses as $status)
							<option {{$status['value'] == $fields['status'] ? 'selected':''}} value="{{$status['value']}}">{{$status['caption']}}</option>
						@endforeach
					</select>
				</x-joona-form-group>

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
					<x-joona-password-validator name="password" policy="{{config('joona.admin_password_policy')}}" />
				</div>
			</div>
		</div>

		<x-slot name="footer">
			<x-joona-button :caption="__('joona::common.save')" icon="check" />
		</x-slot>
	</x-joona-dialog>
</x-joona-form>
