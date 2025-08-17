<x-form id="cropper-form" method="post">
	<x-dialog :caption="__('joona::common.cropper')">
		
		<div class="cropper" data-select-container="#cropper-selector" data-save-button="#crop-save-btn" data-role="cropper.root"></div>

		<x-slot name="header">
			<div id="cropper-selector"></div>
		</x-slot>

		<x-slot name="footer">
			<x-button type="button" :caption="__('joona::common.save')" id="crop-save-btn" icon="check" />
		</x-slot>
	</x-dialog>
</x-form>
