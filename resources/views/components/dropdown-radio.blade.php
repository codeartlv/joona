@php
$selectedLabel = '';

foreach ($options as $option) {
	if ($option->selected) {
		$selectedLabel = $option->label;
		break;
	}
}
@endphp
<div data-bind="components.dropdown-radios" class="form-dropdown-radios dropdown">
	<x-button caption="{{ $selectedLabel }}" role="outline-secondary" class="dropdown-toggle" data-role="dropdown-radios.caption" type="button" data-bs-toggle="dropdown" aria-expanded="false" />

	<ul class="dropdown-menu">
		@foreach ($options as $option)
			<li>
				<label class="dropdown-item">
					<input class="visually-hidden" type="radio" name="{{ $name }}" value="{{ $option->value }}" {{ $option->selected ? 'checked' : '' }} class="form-check-input" />
					<span data-role="dropdown-radios.item-caption">{{ $option->label }}</span>
				</label>
			</li>
		@endforeach
	</ul>
</div>