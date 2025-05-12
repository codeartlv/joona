@extends('joona::blank')

@section('main')
	<div class="card card-auth">
		<x-form action="{{route('joona.user.auth-process')}}" data-focus="email">
			<div class="card-header">
				<span class="card-auth__logo">
					<img class="card-auth__logo-light" src="{{$logo}}" alt="{{ config('app.name', 'Laravel') }}" />
					<img class="card-auth__logo-dark" src="{{$logo_dark}}" alt="{{ config('app.name', 'Laravel') }}" />
				</span>
				<strong>{{ config('app.name', 'Laravel') }}</strong>
				<p>@lang('joona::user.auth.caption')</p>
			</div>
			<div class="card-body">
				<div {!!$logout_message ? 'class="alert alert-danger"':''!!} data-role="form.response">{{$logout_message}}</div>

				<div class="block">
					<div class="form-group">
						<x-input :label="__('joona::user.email')" icon="person" iconPrepend="mail" autocomplete="email" name="email" required="true" />
						<div data-field-message="email"></div>
					</div>

					<div class="form-group">
						<x-input :label="__('joona::user.password')" icon="key" iconPrepend="password" type="password" autocomplete="current-password" name="password" required="true" />
						<div data-field-message="password"></div>
					</div>
				</div>

				<div class="block card-auth__options">
					@include('joona::partials/locale-select')
				</div>
			</div>
			<div class="card-footer">
				<x-button type="submit" role="primary" class="btn-block" :caption="__('joona::user.auth.button')" icon="key" />

				<p class="card-auth__forgot-pass">
					<a href="{{route('joona.user.recover-form')}}">@lang('joona::user.recover.forgot_password')</a>
				</p>
			</div>
		</x-form>
	</div>
</div>
@endsection
