@if ($label)
	<label class="form-label">{{$label}}</label>
@endif
<input class="form-control" name="{{ $name }}" {{ $attributes }} value="{{ $value }}" />
