@if ($label)
	<label class="form-label">{{$label}}</label>
@endif

<div class="password-validator" data-bind="components.passwordValidator" data-policy="{{$policy}}">
	<div class="input-group">
		<input class="form-control" type="password" data-role="password-validator.password-input" name="{{$name}}" autocomplete="new-password" />
		<a href="javascript:;" data-role="password-validator.toggle-visbility" class="input-group-icon">
			@icon('visibility')
			@icon('visibility_off')
		</a>
	</div>

	<div data-field-message="{{$name}}"></div>

	<div class="password-validator__progress">
		<div data-role="password-validator.progress"></div>
	</div>

	<div data-role="form.field-message" data-name="password"></div>

	<ul class="password-validator__steps">
		<li data-step="mixed">
			@icon('close')
			@icon('check')
			@lang('joona::validation.password_hint.mixed')
		</li>
		<li data-step="uppercase">
			@icon('close')
			@icon('check')
			@lang('joona::validation.password_hint.uppercase')
		</li>
		<li data-step="lowercase">
			@icon('close')
			@icon('check')
			@lang('joona::validation.password_hint.lowercase')
		</li>
		<li data-step="special">
			@icon('close')
			@icon('check')
			@lang('joona::validation.password_hint.symbols')
		</li>
		<li data-step="number">
			@icon('close')
			@icon('check')
			@lang('joona::validation.password_hint.numbers')
		</li>
		<li data-step="min">
			@icon('close')
			@icon('check')
			@lang('joona::validation.password_hint.length', ['length' => $min_length])
		</li>
		<li data-step="max">
			@icon('close')
			@icon('check')
			@lang('joona::validation.password_hint.long')
		</li>
	</ul>
</div>
