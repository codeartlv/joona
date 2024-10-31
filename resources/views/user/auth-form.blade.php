@extends('joona::blank')

@section('main')
	<div class="card card-auth">
		<x-form action="{{route('joona.user.auth-process')}}" data-focus="email">
			<div class="card-header">
				<span>
					<img src="{{$logo}}" />
				</span>
				<strong>{{ config('app.name', 'Laravel') }}</strong>
				<p>@lang('joona::user.auth.caption')</p>
			</div>
			<div class="card-body">
				<div {!!$logout_message ? 'class="alert alert-danger"':''!!} data-role="form.response">{{$logout_message}}</div>

				<div class="block">
					<div class="form-group">
						<x-input :label="__('joona::user.email')" icon="person" textPrepend="@" autocomplete="email" name="email" required="true" />
						<div data-field-message="email"></div>
					</div>

					<div class="form-group">
						<x-input :label="__('joona::user.password')" icon="key" iconPrepend="password" type="password" autocomplete="current-password" name="password" required="true" />
						<div data-field-message="password"></div>
					</div>
				</div>

				<div class="block card-auth__options">
					@if (!empty($languages) && count($languages) > 1)
						<section class="card-auth__locale">
							<nav aria-label="breadcrumb">
								<ol class="breadcrumb breadcrumb-languages">
									@foreach ($languages as $language)
										<li class="breadcrumb-item {{$language['active'] ? 'active':''}}">
											<a href="{{$language['url']}}">
												{{$language['title']}}
											</a>
										</li>
									@endforeach
								</ol>
							</nav>
						</section>
					@endif

					<section class="card-auth__lightmode">
						<a href="javascript:;" data-dark-icon="dark_mode" data-light-icon="light_mode" data-bind="admin.colorThemeSwitch">
							@php
								$icon = $theme == 'dark' ? 'light_mode':'dark_mode';
							@endphp

							@icon($icon)
						</a>
					</section>
				</div>
			</div>
			<div class="card-footer">
				<x-button type="submit" role="primary" class="btn-block" :caption="__('joona::user.auth.button')" icon="key" />
			</div>
		</x-form>
	</div>
</div>
@endsection
