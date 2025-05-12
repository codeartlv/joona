@extends('joona::blank')

@section('main')
	<div class="card card-auth">
		<x-form action="{{route('joona.user.recover-start')}}" data-focus="email">
			<div class="card-header">
				<span class="card-auth__logo">
					<img class="card-auth__logo-light" src="{{$logo}}" alt="{{ config('app.name', 'Laravel') }}" />
					<img class="card-auth__logo-dark" src="{{$logo_dark}}" alt="{{ config('app.name', 'Laravel') }}" />
				</span>
				<strong>{{ config('app.name', 'Laravel') }}</strong>
				<p>@lang('joona::user.recover.caption')</p>
			</div>
			<div class="card-body">
				@if ($sent)
					<x-alert role="success">@lang('joona::user.recover.sent')</x-alert>
				@else
					<div data-role="form.response"></div>

					<div class="block">
						<div class="form-group">
							<x-input :label="__('joona::user.email')" iconPrepend="mail" autocomplete="email" name="email" required />
							<div data-field-message="email"></div>
							<div class="form-text">@lang('joona::user.recover.email_hint')</div>
						</div>
					</div>
				@endif
				
			</div>
			@if (!$sent)
				<div class="card-footer">
					<x-button type="submit" role="primary" class="btn-block" :caption="__('joona::user.recover.submit_start')" />
				</div>
			@endif
		</x-form>
	</div>
</div>
@endsection
