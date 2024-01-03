@extends('joona::global')

@section('html_body')
	<main class="layout layout-blank">
		<aside>
			<div class="container-fluid">
				@yield('main')
			</div>
		</aside>
	</main>
@endsection
