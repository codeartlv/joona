<div class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title">{{$caption}}</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('joona::common.close')">
			@icon('close')
		</button>
	</div>
	<div class="modal-body">
		<div class="modal-inner">
			{{ $slot }}
		</div>
	</div>
	@isset($footer)
	<div class="modal-footer">
		{{ $footer }}
	</div>
	@endisset
</div>
