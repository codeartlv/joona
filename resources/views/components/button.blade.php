<button {{ $attributes->merge($custom_attr) }}>
	@if ($icon)
		@icon($icon)
	@endif
	{{$caption}}
</button>
