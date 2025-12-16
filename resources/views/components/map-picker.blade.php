@php
$params = $attributes->getAttributes();

$params += [
	'data-required' => 'true',
];

$required = $params['data-required'] === 'true';

@endphp
<div data-bind="components.map-picker" class="map-picker {{ $class }}" {{ $attributes }}>
    <input name="{{ $name }}" type="hidden" data-role="value" value="{{ $value }}" />
	@if(!$required)
		<div class="map-picker__options">
			<x-button role="outline-dark" data-role="map-picker.clear" type="button" class="btn-sm map-picker__delete" icon="delete"></x-button>
		</div>
	@endif
</div>
