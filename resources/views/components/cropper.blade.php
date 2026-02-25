<x-offcanvas :caption="__('joona::common.cropper')">
	<x-form id="cropper-form" method="post">
		<div class="cropper" data-select-container="#cropper-selector" data-save-button="#crop-save-btn" data-role="cropper.root"></div>
	</x-form>

	<x-slot name="header">
		<div id="cropper-selector"></div>
	</x-slot>

	<x-slot name="footer">
		<x-button form="cropper-form" type="button" :caption="__('joona::common.save')" id="crop-save-btn" icon="check" />
	</x-slot>	
</x-offcanvas>

