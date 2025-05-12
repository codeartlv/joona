@extends('joona::blank')

@section('main')
	<div class="card card-auth">
		<x-form action="{{route('joona.user.recover-finish')}}" data-focus="email">
			<input type="hidden" name="token" value="{{$token}}" />
			<input type="hidden" name="email" value="{{$provided_email}}" />

			<div class="card-header">
				<span class="card-auth__logo">
					<img class="card-auth__logo-light" src="{{$logo}}" alt="{{ config('app.name', 'Laravel') }}" />
					<img class="card-auth__logo-dark" src="{{$logo_dark}}" alt="{{ config('app.name', 'Laravel') }}" />
				</span>
				<strong>{{ config('app.name', 'Laravel') }}</strong>
				<p>@lang('joona::user.recover.caption')</p>
			</div>
			<div class="card-body">
				<div data-role="form.response"></div>

				<div class="block">
					<div class="form-group">
						<x-password-validator :label="__('joona::user.password')" name="password" policy="{{config('joona.admin_password_policy')}}" />
					</div>
				</div>
			</div>
			<div class="card-footer">
				<x-button type="submit" role="primary" class="btn-block" :caption="__('joona::user.recover.set_password')" icon="key" />
			</div>
		</x-form>
	</div>
</div>
@endsection
