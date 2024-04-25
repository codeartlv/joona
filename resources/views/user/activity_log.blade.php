<x-content :title="__('joona::user.activity_log_page_title')">
	<x-slot name="sidebar">
		<form action="" method="get">
			<div class="card">
				<div class="card-body">
					<div class="form-group">
						<x-select name="user_id" required="true" :label="__('joona::user.user')" :options="$users" blank />
					</div>

					<div class="form-group">
						<x-datepicker :value="$date_from" required="true" :label="__('joona::common.date_from')" name="date_from" />
					</div>

					<div class="form-group required">
						<x-datepicker :value="$date_to" name="date_to" required="true" :label="__('joona::common.date_to')" />
					</div>
				</div>
				<div class="card-footer">
					<x-button :caption="__('joona::common.filter')" icon="search" />
				</div>
			</div>
		</form>
	</x-slot>

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
			<x-alert role="info">{{__('joona::user.report_no_sessions')}}</x-alert>
		@endif
	@else
		<x-alert role="info">{{__('joona::user.report_choose_user_and_date_range')}}</x-alert>
	@endif

	<x-slot name="footer">
		<div class="navbar bottom">
			<ul class="navbar-nav">
				<li class="nav-item">
					@choice('joona::common.records', $total, [$total])
				</li>
			</ul>
		</div>
	</x-slot>
</x-content>
