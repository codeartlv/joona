@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif
<input class="form-control" name="{{ $name }}" {{ $attributes }} value="{{ $value }}" />
