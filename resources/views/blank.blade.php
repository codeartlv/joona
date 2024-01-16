@extends('joona::global')

@section('html_body')
<main class="layout layout-split">
	<figure class="auth-bg-image" style="background-image:url('/vendor/joona/images/backgrounds/w{{intval(date('W'))}}.jpg">

	</figure>
	<aside>
		<div class="container-fluid">
			@yield('main')
		</div>
	</aside>
</main>

<style>
	html[data-bs-theme="dark"] {
		.auth-bg-image {
			background-image:url('/vendor/joona/images/backgrounds/dark_w{{intval(date('W'))}}.jpg') !important;
		}
	}
</style>
@endsection
