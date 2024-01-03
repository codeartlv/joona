@if ($label)
	<label class="form-label">{{$label}}</label>
@endif

<input type="color" class="form-control form-control-color" name="{{$name}}" {{$attributes}} />
