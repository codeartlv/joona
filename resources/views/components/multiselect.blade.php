@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

<div class="dropdown form-multiselect" data-bind="components.multi-select">
	<div class="form-control" data-bs-toggle="dropdown" aria-expanded="false">
		<em data-role="selected-text"></em>
	</div>
	<ul class="dropdown-menu">
		@foreach ($options as $option)
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
	</ul>
</div>
