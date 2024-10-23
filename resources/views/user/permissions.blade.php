<x-content :title="__('joona::user.permissions_page_title')">
	<x-slot name="controls">
		<x-button :caption="__('joona::user.permissions.add_role')" icon="group_add" data-bind="admin.roleEdit" data-id="0" />
	</x-slot>

	<div class="block">
		<div class="row">
			<div class="col-12 col-md-3 mb-3 mb-md-0">
				<div class="card">
					<div class="card-header">
						@lang('joona::user.permissions.roles')
					</div>
					@if ($roles->count())
						<nav class="list-group list-group-flush">
							@foreach ($roles as $role)
								<div class="list-group-item {{$role_id == $role['id'] ? 'active':''}}">
									<a href="{{route('joona.user.permission-groups', ['role_id' => $role['id']])}}">
										{{$role['title']}}
									</a>
									<nav>
										<a href="javascript:;" data-bind="admin.roleEdit" data-id="{{$role['id']}}">
											<i class="material-symbols-outlined">
												edit
											</i>
										</a>
										<a href="{{route('joona.user.permission-delete-role', ['id' => $role['id']])}}" data-bind="admin.confirm" data-message="@lang('joona::user.permissions.confirm_role_delete')">
											<i class="material-symbols-outlined">
												delete
											</i>
										</a>
									</nav>
								</div>
							@endforeach
						</nav>
					@else
						<div class="card-body">
							<x-alert role="info" class="mb-0">{{__('joona::user.permissions.no_roles')}}</x-alert>
						</div>
					@endif
				</div>
			</div>
			<div class="col-12 col-md-9">
				@if ($role_id)
					<x-form action="{{route('joona.user.permission-save')}}">
						<input type="hidden" name="role_id" value="{{$role_id}}" />
						<div class="card">
							<div class="card-header">@lang('joona::user.permissions.permissions')</div>
							<div class="card-body">
								<div data-role="form.response"></div>

								@foreach ($permissions as $group)
									<x-form-section-heading :label="$group['label']"/>

									<div class="form-group">
										@foreach ($group['permissions'] as $permission)
											<x-checkbox :checked="in_array($permission['id'], $selected)" name="permissions[]" :value="$permission['id']" :label="$permission['label']"  />
										@endforeach
									</div>
								@endforeach
							</div>
							<div class="card-footer">
								<x-button :caption="__('joona::common.save')" icon="check" />
							</div>
						</div>
					</x-form>
				@else
					<x-alert role="info">{{__('joona::user.permissions.no_role_selected')}}</x-alert>
				@endif
			</div>
		</div>
	</div>
</x-content>


@section('main')

@endsection
