<button {{ $attributes->merge(['class' => 'btn btn-'.$role, 'type' => 'submit']) }}>
	@if ($icon)
		@icon($icon)
	@endif
	{{$caption}}
</button>
