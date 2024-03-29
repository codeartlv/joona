@extends('joona::simple')

@section('page_title', __('joona::user.permissions_page_title'))

@section('main')
	<div class="block">
		<div class="row">
			<div class="col-12 col-md-3 mb-3 mb-md-0">
				<div class="card">
					<div class="card-header">
						@lang('joona::user.permissions.roles')
						<span>
							<a href="javascript:;" class="btn btn-xs btn-outline-primary" x-data="adminRoleEdit" data-id="0">
								<i class="material-symbols-outlined">group_add</i>
								@lang('joona::user.permissions.add_role')
							</a>
						</span>
					</div>
					@if ($roles->count())
						<nav class="list-group list-group-flush">
							@foreach ($roles as $role)
								<div class="list-group-item {{$role_id == $role['id'] ? 'active':''}}">
									<a href="{{route('joona.user.permission-groups', ['role_id' => $role['id']])}}">
										{{$role['title']}}
									</a>
									<nav>
										<a href="javascript:;" x-data="adminRoleEdit" data-id="{{$role['id']}}">
											<i class="material-symbols-outlined">
												edit
											</i>
										</a>
										<a href="{{route('joona.user.permission-delete-role', ['id' => $role['id']])}}" x-data="confirm" data-message="@lang('joona::user.permissions.confirm_role_delete')">
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
							<div class="alert alert-info mb-0">@lang('joona::user.permissions.no_roles')</div>
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
									<div class="form-split">
										<span>{{$group['label']}}</span>
									</div>

									<div class="form-group">
										@foreach ($group['permissions'] as $permission)
											<div class="form-check">
												<input class="form-check-input" name="permissions[]" {{in_array($permission['id'], $selected) ? 'checked':''}} type="checkbox" value="{{$permission['id']}}" id="p{{$permission['id']}}">
												<label class="form-check-label" for="p{{$permission['id']}}">
													{{$permission['label']}}
												</label>
											</div>
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
					<div class="alert alert-info">@lang('joona::user.permissions.no_role_selected')</div>
				@endif
			</div>
		</div>
	</div>
@endsection
