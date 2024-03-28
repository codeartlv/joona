<select class="form-select" name="{{$name}}">
	@foreach ($options as $option)
		<option value="{{$option->value}}" {{$option->selected?'selected':''}}>{{$option->label}}</option>
	@endforeach
</select>
