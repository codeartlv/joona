@extends('joona::simple')

@section('page_title', __('joona::user.user_list_page_title'))

@section('foot')
<div class="navbar bottom">
	<ul class="navbar-nav">
		<li class="nav-item">
			@choice('joona::common.records', $total, [$total])
		</li>
	</ul>
	<ul class="navbar-nav">
		<li class="nav-item">
			<x-paginator :total="$total" :size="$size" />
		</li>
	</ul>
</div>
@endsection

@section('controls')
	@can('admin_edit_users')
		<x-button :caption="__('joona::user.create_new')" icon="person_add" :attr="['x-data' => 'adminUserEdit', 'data-id' => 0]" />
	@endif
@endsection

@section('main')
<div class="block">
	<div class="table-responsive">
		<table class="table table-striped table-mobile-stacked">
			<thead>
				<tr>
					<th>@lang('joona::common.id')</th>
					<th>@lang('joona::user.email')</th>
					<th>@lang('joona::user.first_name')</th>
					<th>@lang('joona::user.last_name')</th>
					@if ($uses_permissions)
						<th>@lang('joona::user.groups')</th>
					@endif
					<th>@lang('joona::user.last_seen')</th>
					<th>@lang('joona::user.last_ip')</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				@foreach ($users as $user)
					<tr>
						<td class="table__id">
							<div class="table__mcaption">@lang('joona::common.id')</div>
							{{$user['id']}}
						</td>

						<td>
							<div class="table__mcaption">@lang('joona::user.email')</div>
							@if ($user['can_manage'] && Gate::allows('admin_edit_users'))
								<a href="javascript:;" x-data="adminUserEdit" data-id="{{$user['id']}}">{{$user['email']}}</a>
							@else
								{{$user['email']}}
							@endif
						</td>
						<td>
							<div class="table__mcaption">@lang('joona::user.first_name')</div>
							{{$user['first_name']}}
						</td>
						<td>
							<div class="table__mcaption">@lang('joona::user.last_name')</div>
							{{$user['last_name']}}
						</td>
						@if ($uses_permissions)
							<td>
								<div class="table__mcaption">@lang('joona::user.groups')</div>
								@if ($user['level'] == 'admin')
									<span class="badge bg-danger">@lang('joona::user.level_names.admin')</span>
								@else
									@foreach ($user['roles'] as $role)
										<span class="badge rounded-pill text-bg-light">{{ $role['title'] }}</span>
									@endforeach
								@endif
							</td>
						@endif
						<td class="table__date">
							<div class="table__mcaption">@lang('joona::user.last_seen')</div>
							{{$user['logged_at'] ? date('d.m.Y H:i', strtotime($user['logged_at'])) : __('joona::common.never')}}
						</td>
						<td>
							<div class="table__mcaption">@lang('joona::user.last_ip')</div>
							{{$user['logged_ip'] ? $user['logged_ip'] : '-'}}
						</td>
						<td class="table__options">
							@php
								$options = [];
							@endphp

							@can('admin_view_userlog')
								@php
								$options[] = [
									'caption' => __('joona::user.activity_log'),
									'icon' => 'browse_activity',
									'href' => route('joona.user.activities', ['user_id' => $user['id']])
								]
								@endphp
							@endif

							@include('joona::components/table-dropdown', [
								'options' => $options
							])
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@endsection
