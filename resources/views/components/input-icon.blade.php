<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>

<div class="input-group">
	<span class="input-group-icon">
		@icon($icon)
	</span>
	<input class="form-control" name="{{ $name }}" {{ $attributes }} value="{{ $value }}" />
</div>


