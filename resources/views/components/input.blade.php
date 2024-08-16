@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif
<input class="form-control form-control-{{ $size }}" name="{{ $name }}" {{ $attributes }} value="{{ $value }}" />
