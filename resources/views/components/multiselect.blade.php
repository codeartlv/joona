@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

<div class="dropdown form-multiselect" data-bind="components.multi-select">
	<div class="form-control" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
		<em data-role="selected-text"></em>
	</div>
	<ul class="dropdown-menu">
		@foreach ($options as $item)
			@if (method_exists($item, 'options'))
				@if ($item->selectable)
					<li class="form-multiselect__group">
						<x-checkbox :checked="$item->selected" :disabled="$item->disabled" label="{{$item->label}}" value="{{$item->value}}" name="{{ $name }}"/>
					</li>
				@else
					<li class="form-multiselect__group-label">{{$item->label}}</li>
				@endif

				@php
					$entries = $item->options();
				@endphp
			@else
				@php
					$entries = [$item];
				@endphp
			@endif

			@foreach ($entries as $option)
				<li>
					<x-checkbox
					:checked="$option->selected"
					:disabled="$option->disabled"
					label="{{$option->label}}"
					value="{{$option->value}}"
					name="{{ $name }}"
				/>
				</li>
			@endforeach
		@endforeach
	</ul>
</div>
