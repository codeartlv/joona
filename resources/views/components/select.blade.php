@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif
<select class="form-select {{$size ? 'form-select-'.$size:''}}" {{ $attributes }}>
	@foreach ($options as $item)
		@if (method_exists($item, 'options'))
			<optgroup label="{{$item->label}}">
				@foreach ($item->options() as $option)
					<option value="{{$option->value}}" {{$option->disabled ? 'disabled':''}} {{$option->selected?'selected':''}}>{{$option->label}}</option>
				@endforeach
			</optgroup>
		@else
			<option value="{{$item->value}}" {{$item->disabled ? 'disabled':''}} {{$item->selected?'selected':''}}>{{$item->label}}</option>
		@endif
	@endforeach
</select>
