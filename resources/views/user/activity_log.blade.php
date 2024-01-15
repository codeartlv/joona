@extends('joona::sidebar')

@section('page_title', __('joona::user.activity_log_page_title'))

@section('sidebar')
	<form action="" method="get">
		<div class="card">
			<div class="card-body">
				<x-joona-form-group required="true" :label="__('joona::user.user')">
					<select name="user_id" class="form-select">
						@foreach ($users as $user)
							<option value="{{$user['id']}}" {{$user['id'] == $user_id ? 'selected':''}}>
								{{$user['username']}}
							</option>
						@endforeach
					</select>
				</x-joona-form-group>

				<x-joona-form-group required="true" :label="__('joona::common.date_from')">
					<div data-bind="components.calendar" class="input-group">
						<input type="text" data-role="control" class="form-control" />
						<input type="hidden" data-role="value" name="date_from" value="{{$date_from}}" />
					</div>
				</x-joona-form-group>

				<x-joona-form-group required="true" class="mb-0" :label="__('joona::common.date_to')">
					<div data-bind="components.calendar" class="input-group">
						<input type="text" data-role="control" class="form-control" />
						<input type="hidden" data-role="value" name="date_to" value="{{$date_to}}" />
					</div>
				</x-joona-form-group>
			</div>
			<div class="card-footer">
				<x-joona-button :caption="__('joona::common.filter')" icon="search" />
			</div>
		</div>
	</form>
@endsection

@section('foot')
<div class="navbar bottom">
	<ul class="navbar-nav">
		<li class="nav-item">
			@choice('joona::common.records', $total, [$total])
		</li>
	</ul>
</div>
@endsection

@section('main')
	@if ($can_display)
		@if (!empty($sessions))
			<div class="block">
				@foreach ($sessions as $session)
					<div class="card card-admin-user-session block">
						<div class="card-header">
							<section>
								<h6>{{$session['started_date']}}</h6>
								<time>
									{{$session['duration_str']}}
									<span class="badge rounded-pill text-bg-light">{{$session['end_reason']}}</span>
								</time>
							</section>
							<div>
								@icon('dns')
								{{$session['ip']}}
							</div>
						</div>
						<table class="table card-body table-striped">
							@foreach ($session['entries'] as $entry)
							<tr>
								<td class="card-admin-user-session__date">{{$entry['date']}}</td>
								<td class="card-admin-user-session__action">{{$entry['action']}}</td>
								<td>{{$entry['object']}}</td>
							</tr>
							@endforeach
						</table>
					</div>
				@endforeach
			</div>
		@else
			<div class="alert alert-info">{{__('joona::user.report_no_sessions')}}</div>
		@endif
	@else
		<div class="alert alert-info">{{__('joona::user.report_choose_user_and_date_range')}}</div>
	@endif
@endsection
