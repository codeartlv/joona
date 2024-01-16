<!DOCTYPE html>
<html class="loading page @yield('html_class')" lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="{{$theme}}">
    <head>
        <meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <title>
			@hasSection('page_title')
				@yield('page_title') -
			@endif
			{{ config('app.name', 'Laravel') }} - @lang('joona::common.app_title')
		</title>
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<script>
			const DATA_URL = '{{route('joona.get-data')}}';
		</script>

		@vite(['resources/css/app.scss', 'resources/js/app.js'])

		@yield('html_head')
    </head>
	<body>
		@yield('html_body')
    </body>
</html>
