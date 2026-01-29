@extends('joona::default')

@section('header_nav')
	<li class="header-nav__sidebar-trigger header-nav__rounded">
		<a href="javascript:;" data-bind="admin.toggleSidebar" data-sidebar="#sidebar">
			<figure>
				@icon('filter_alt')
			</figure>
		</a>
	</li>
@endsection

@section('content_main')
	<div class="content with-sidebar">
		<div class="content__sidebar" id="sidebar">
			<div class="content__sidebar-inner" data-bind="admin.sidebar">
				@yield('sidebar')
			</div>
		</div>
		<div class="content__inner">
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
