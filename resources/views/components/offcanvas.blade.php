<div class="offcanvas-header">
	<h5 class="offcanvas-title">{{$caption}}</h5>
	<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close">
		@icon('close')
	</button>
</div>

<div class="offcanvas-body">
	{{ $slot }}
</div>

@isset($footer)
<div class="offcanvas-footer">
	{{ $footer }}
</div>
@endisset
