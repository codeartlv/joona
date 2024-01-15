@extends('joona::global')

@section('html_body')
<main class="layout layout-split">
		<figure class="auth-bg-image-w{{intval(date('W'))}}">

		</figure>
		<aside>
			<div class="container-fluid">
				@yield('main')
			</div>
		</aside>
	</main>
@endsection
