@extends('joona::sidebar')

@section('page_title', __('joona::user.activity_log_page_title'))

@section('sidebar')
	<form action="" method="get">
		<div class="card">
			<div class="card-body">
				<div class="form-group required">
					<label>@lang('joona::user.user')</label>
					<x-select name="user_id" :options="$users" blank />
				</div>

				<div class="form-group required">
					<label>@lang('joona::common.date_from')</label>
					<x-datepicker :value="$date_from" name="date_from" />
				</div>

				<div class="form-group required">
					<label>@lang('joona::common.date_to')</label>
					<x-datepicker :value="$date_to" name="date_to" />
				</div>
			</div>
			<div class="card-footer">
				<x-button :caption="__('joona::common.filter')" icon="search" />
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
								<div>
									@icon('dns')
									{{$session['ip']}}
								</div>
								<div class="card-admin-user-session__agent">
									@icon('computer')
									{{$session['agent']['browser']}} @ {{$session['agent']['platform']}}, {{$session['agent']['device']}}
								</div>
							</div>
						</div>
						<table class="table card-body table-striped">
							@foreach ($session['entries'] as $entry)
							<tr>
								<td class="card-admin-user-session__date">{{$entry['date']}}</td>
								<td class="card-admin-user-session__action">{{$entry['action']}}</td>
								<td>{!!$entry['object']!!}</td>
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
