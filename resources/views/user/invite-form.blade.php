@extends('joona::blank')

@section('main')
	<div class="card card-auth">
		<x-form action="{{route('joona.user.invite-process')}}" data-focus="email">
			<input type="hidden" name="hash" value="{{$hash}}" />
			<div class="card-header">
				<span class="card-auth__logo">
					<img class="card-auth__logo-light" src="{{$logo}}" alt="{{ config('app.name', 'Laravel') }}" />
					<img class="card-auth__logo-dark" src="{{$logo_dark}}" alt="{{ config('app.name', 'Laravel') }}" />
				</span>
				<strong>{{ config('app.name', 'Laravel') }}</strong>
				<p>@lang('joona::user.invite.caption')</p>
			</div>
			<div class="card-body">
				<div data-role="form.response"></div>

				<div class="block">
					<div class="form-group">
						<x-input :label="__('joona::user.email')" iconPrepend="mail" autocomplete="email" name="email" readonly disabled :value="$user_email" />
						<div data-field-message="email"></div>
					</div>

					<div class="form-group">
						<x-input :label="__('joona::user.first_name')" iconPrepend="person" required="true" name="first_name" :value="$fields['first_name'] ?? ''" autocomplete="name" maxlength="55" type="text" />
					</div>
				
					<div class="form-group">
						<x-input :label="__('joona::user.last_name')" iconPrepend="person" required="true" name="last_name" :value="$fields['last_name'] ?? ''" autocomplete="family-name" maxlength="55" type="text" />
					</div>
				
					<div class="form-group">
						<x-password-validator :label="__('joona::user.password')" name="password" policy="{{config('joona.admin_password_policy')}}" />
					</div>
				</div>

				<div class="block card-auth__options">
					@include('joona::partials/locale-select')
				</div>
			</div>
			<div class="card-footer">
				<x-button type="submit" role="primary" class="btn-block" :caption="__('joona::user.invite.register')" icon="key" />
			</div>
		</x-form>
	</div>
</div>
@endsection
