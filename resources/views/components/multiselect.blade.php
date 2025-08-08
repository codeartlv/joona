@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

<div class="dropdown form-multiselect" data-bind="components.multi-select">
	<div class="form-control" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
		<em data-role="selected-text"></em>
	</div>
	<ul class="dropdown-menu">
		@if (!empty($options))
			@if ($type == 'checkbox')
				<li class="form-multiselect__toggle-all">
					<x-checkbox data-role="toggle" :label="__('joona::common.toggle_all')" />
				</li>
			@endif

			@foreach ($options as $item)
				@if (method_exists($item, 'options'))
					@if ($item->selectable)
						<li class="form-multiselect__group">
							<x-checkbox type="{{$type}}" data-role="option" :checked="$item->selected" :disabled="$item->disabled" label="{{$item->label}}" value="{{$item->value}}" name="{{ $name }}"/>
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
					<li class="form-multiselect__option">
						<x-checkbox
						data-role="option"
						:checked="$option->selected"
						:disabled="$option->disabled"
						label="{{$option->label}}"
						value="{{$option->value}}"
						type="{{$type}}"
						name="{{ $name }}"
					/>
					</li>
				@endforeach
			@endforeach
		@endif
	</ul>
</div>
