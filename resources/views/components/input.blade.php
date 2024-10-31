@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

@if ($iconPrepend || $textPrepend || $iconAppend || $textAppend)
	<div class="input-group">
		@if ($iconPrepend || $textPrepend)
			<span class="input-group-text">
				@if ($iconPrepend)
					@icon($iconPrepend)
				@else
					{{$textPrepend}}
				@endif
			</span>
		@endif
		<input class="form-control form-control-{{ $size }}" name="{{ $name }}" {{ $attributes }} value="{{ $value }}" />
		@if ($iconAppend || $textAppend)
			<span class="input-group-text">
				@if ($iconAppend)
					@icon($iconAppend)
				@else
					{{$textAppend}}
				@endif
			</span>
		@endif
	</div>
@else
	<input class="form-control form-control-{{ $size }}" name="{{ $name }}" {{ $attributes }} value="{{ $value }}" />
@endif
