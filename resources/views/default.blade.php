@extends('joona::global')

@section('html_body')
	<main class="layout layout-default">
		<aside class="main-menu" data-bs-theme="dark">
			<div class="main-menu__logo">
				<img src="{{$icon}}" />
				<span>
					{{ $app_title }}
					<small>@lang('joona::common.admin_caption')</small>
				</span>
			</div>
			<div class="main-menu__content">
				<div class="main-menu__inner">
					@include('joona::menu')
				</div>
			</div>
			<div class="main-menu__footer">
				@include('joona::copyright')
			</div>
		</aside>

		<div id="mobile-menu" class="offcanvas offcanvas-mobile-menu offcanvas-end offcanvas-md">
			<div class="offcanvas-header">

			</div>
			<div class="offcanvas-body" data-bs-theme="dark">
				<div class="offcanvas-inner">
					@include('joona::common.topbar')
					@include('joona::menu')

					@if (!empty($languages) && count($languages) > 1)
						<ul class="nav main-nav">
							<li class="nav-item nav-group">
								@lang('joona::common.language')
							</li>
							@foreach ($languages as $language)
								<li class="nav-item">
									<a class=" {{$language['active'] ? 'active':''}}" href="{{$language['url']}}">
										<img src="{{$language['flag']}}" />
										<span>
											{{$language['title']}}
										</span>
									</a>
								</li>
							@endforeach
						</ul>						
					@endif

					<ul class="nav main-nav">
						<li class="nav-item nav-group">
							@lang('joona::common.theme')
						</li>
						<li class="nav-item">
							<a href="javascript:;" data-bind="admin.colorThemeSwitch" data-theme="light">
								@icon('light_mode')
								<span>@lang('joona::common.theme_light')</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="javascript:;" data-bind="admin.colorThemeSwitch" data-theme="dark">
								@icon('dark_mode')
								<span>@lang('joona::common.theme_dark')</span>
							</a>
						</li>
					</ul>

					<ul class="nav main-nav">
						<li class="nav-item nav-group">
							@lang('joona::common.my_profile')
						</li>
						<li class="nav-item">
							<a href="javascript:;" data-bind="admin.editMyProfile">
								@icon('person_edit')
								<span>@lang('joona::common.edit_my_profile')</span>
							</a>
						</li>
						<li class="nav-item">
							<a href="{{route('joona.user.logout')}}">
								@icon('lock')
								<span>@lang('joona::user.logout')</span>
							</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="offcanvas-footer">
				@include('joona::copyright')
			</div>
		</div>

		<div class="layout__content">
			<header class="main-header">
				<div class="container-fluid">
					<div class="main-header__content">
						<section class="main-header__first-sec">
							<img class="main-header__logo" src="{{$logo}}" />
						</section>
						<section class="main-header__menu">
							@include('joona::common.topbar')
							<section class="main-header__side-nav">
								<ul class="header-nav">
									<li class="header-nav__name">
										<strong>{{$name}}</strong>
										{{$email}}
									</li>

									@if (!empty($languages) && count($languages) > 1)
										<li class="dropdown header-nav__rounded header-nav__languages">
											<a href="javascript:;" data-bs-toggle="dropdown" data-expanded="false">
												<figure>
													@foreach ($languages as $language)
														@if ($language['active'])
															<img src="{{$language['flag']}}" />
														@endif
												@endforeach
												</figure>
											</a>
											<div class="dropdown-menu">
												@foreach ($languages as $language)
													<a class="dropdown-item {{$language['active'] ? 'active':''}}" href="{{$language['url']}}">
														<img src="{{$language['flag']}}" />
														{{$language['title']}}
													</a>
												@endforeach
											</div>
										</li>
									@endif

									<li class="header-nav__rounded header-nav__theme">
										<a href="javascript:;" data-dark-icon="dark_mode" data-light-icon="light_mode" data-bind="admin.colorThemeSwitch">
											<figure>
												<i data-role="icon" class="material-symbols-outlined">{{$theme == 'dark' ? 'light_mode':'dark_mode'}}</i>
											</figure>
										</a>
									</li>

									<li class="header-nav__rounded header-nav__notifications" data-bind="admin.notification-list">
										<a href="#notification-list" data-bs-toggle="dropdown" data-expanded="false">
											<figure>
												@icon('notifications')
												<span class="badge bg-primary"></span>
											</figure>
										</a>
										<div data-role="notification-list.container" class="dropdown-menu notification-list">
										</div>
									</li>									

									<li class="header-nav__rounded header-nav__profile-edit">
										<a href="javascript:;" data-bind="admin.editMyProfile">
											<figure>
												@icon('person_edit')
											</figure>
										</a>
									</li>
									<li class="header-nav__lock header-nav__rounded  header-nav__logout">
										<a href="{{route('joona.user.logout')}}">
											<figure>
												@icon('lock')
											</figure>
										</a>
									</li>
									@yield('header_nav')
									<li class="header-nav__mobile-menu">
										<a href="#mobile-menu" data-bs-toggle="offcanvas" role="button">
											@icon('menu')
										</a>
									</li>
								</ul>
							</section>
						</section>
					</div>
				</div>
			</header>

			@yield('content_main')
		</div>
	</main>
@endsection
