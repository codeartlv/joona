@if ($label)
	<label class="form-label">{{$label}}</label>
@endif
<select class="form-select {{$size ? 'form-select-'.$size:''}}" {{ $attributes }}>
	@foreach ($options as $option)
		<option value="{{$option->value}}" {{$option->disabled ? 'disabled':''}} {{$option->selected?'selected':''}}>{{$option->label}}</option>
	@endforeach
</select>
