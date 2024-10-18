<button {{ $attributes->merge([
		'class' => 'btn btn-'.$role.(!$caption ? ' captionless':''),
		'type' => 'submit'
	]) }}>
	@if ($icon)
		@icon($icon)
	@endif
	{{$caption}}
</button>
