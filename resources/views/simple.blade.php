@extends('joona::default')

@section('content_main')
	<div class="content">
		<div class="content__inner" {{$attributes}}>
			<div class="content__panel">
				<h1>@yield('page_title')</h1>
				<nav>@yield('controls')</nav>
			</div>
			<div class="content__main">
				@yield('main')
			</div>

			@yield('foot')
		</div>
	</div>
@endsection
