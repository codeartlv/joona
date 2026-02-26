<x-content :title="__('joona::user.user_list_page_title')">
	<x-slot name="controls">
		@can('admin_edit_users')
			<x-button :caption="__('joona::user.create_new')" icon="person_add" data-bind="admin.userEdit" data-id="0" />
		@endif
	</x-slot>

	<x-slot name="sidebar">
		<form action="" method="get">
			<div class="card">
				<div class="card-header">@lang('joona::common.data_filter')</div>
				<div class="card-body">
					<div class="form-group">
						<x-input :label="__('joona::common.search')" name="search" :value="$search" />
					</div>
				</div>
				<div class="card-footer">
					<x-button type="submit" icon="search" :caption="__('joona::common.search')" />
				</div>
			</div>
		</form>
	</x-slot>

	<div class="block">
		<table class="table table-striped table-mobile-stacked">
			<thead>
				<tr>
					<th>@lang('joona::common.id')</th>
					<th>@lang('joona::user.email')</th>
					<th>@lang('joona::user.first_name')</th>
					<th>@lang('joona::user.last_name')</th>
					<th>@lang('joona::user.status')</th>
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
								<a href="javascript:;" data-bind="admin.userEdit" data-id="{{$user['id']}}">{{$user['email']}}</a>
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
						<td>
							<div class="table__mcaption">@lang('joona::user.status')</div>
							<span class="badge bg-{{$user['status_class']}}">{{$user['status_label']}}</span>
						</td>
						@if ($uses_permissions)
							<td>
								<div class="table__mcaption">@lang('joona::user.groups')</div>
								@if ($user['level'] == 'admin')
									<span class="badge bg-danger">@lang('joona::user.level_names.admin')</span>
								@else
									@foreach ($user['roles'] as $role)
										<span class="badge text-bg-light">{{ $role['title'] }}</span>
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

								$extraOptions = \Codeart\Joona\Facades\Joona::getUserOptions($user['user_object']);
								if (!empty($extraOptions)) {
									$options = array_merge($options, $extraOptions);
									$options[] = '-';
								}
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

							@php
							if (Gate::allows('admin_edit_users') && $user['can_manage']) {
								$options[] = '-';

								$options[] = [
									'caption' => __('joona::user.delete_user'),
									'icon' => 'delete',
									'href' => route('joona.user.delete', ['user_id' => $user['id']]),
									'attributes' => [
										'data-bind' => 'admin.confirm',
										'data-message' => __('joona::user.delete_confirm'),
									]
								];
							}
							
							@endphp

							@include('joona::components/table-dropdown', [
								'options' => $options
							])
						</td>
					</tr>
				@endforeach
			</tbody>
		</table>

	</div>

	<x-slot name="footer">
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
	</x-slot>
</x-content>

