@extends('joona::blank')

@section('main')
	<div class="card card-error">
		<div class="card-header">
			@lang('joona::common.error')
		</div>
		<div class="card-body">
			{{$message}}
		</div>
		<div class="card-footer">
			<a class="btn btn-outline-secondary" href="/">@lang('joona::common.go_home')</a>
		</div>
	</div>
</div>
@endsection
