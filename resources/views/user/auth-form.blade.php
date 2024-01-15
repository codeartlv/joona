@extends('joona::blank')

@section('main')
	<div class="card card-auth">
		<x-joona-form :action="route('joona.user.auth-process')" focus="username">
			<input type="hidden" name="continue" value="{{$continue}}" />

			<div class="card-header">
				<span>
					<img src="{{config('joona.app_logo')}}" />
				</span>
				<strong>{{ config('app.name', 'Laravel') }}</strong>
				<p>@lang('joona::user.auth.caption')</p>
			</div>
			<div class="card-body">

				<div {!!$logout_message ? 'class="alert alert-danger"':''!!} data-role="form.response">{{$logout_message}}</div>

				<div class="block">
					<x-joona-form-group required="true" :label="__('joona::user.username')">
						<div class="input-group">
							<span class="input-group-icon">
								@icon('person')
							</span>
							<input class="form-control" type="text" autocomplete="username" name="username" />
						</div>
						<div data-field-message="username"></div>
					</x-joona-form-group>

					<x-joona-form-group required="true" :label="__('joona::user.password')">
						<div class="input-group">
							<span class="input-group-icon">
								@icon('key')
							</span>
							<input class="form-control" type="password" autocomplete="current-password" name="password" />
						</div>
						<div data-field-message="password"></div>
					</x-joona-form-group>
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
						<a href="javascript:;" data-dark-icon="dark_mode" data-light-icon="light_mode" data-bind="admin.color-theme-switch">
							<i data-role="icon" class="material-symbols-outlined">{{$theme == 'dark' ? 'light_mode':'dark_mode'}}</i>
						</a>
					</section>
				</div>
			</div>
			<div class="card-footer">
				<x-joona-button type="submit" role="primary" :block="true" :caption="__('joona::user.auth.button')" icon="key" />
			</div>
		</x-joona-form>
	</div>
@endsection
