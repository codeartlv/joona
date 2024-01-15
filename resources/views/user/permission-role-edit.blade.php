<x-joona-form :action="route('joona.user.permission-save-role')" class="modal-content" focus="title">
	<input type="hidden" name="id" value="{{$id}}" />

	<div class="modal-header">
		<h5 class="modal-title">@lang('joona::user.permissions.edit_role')</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
			@icon('close')
		</button>
	</div>
	<div class="modal-body" data-bind="admin.user-edit-form">
		<div class="modal-inner">
			<div data-role="form.response"></div>

			<div class="block">
				<x-joona-form-group required="true" :label="__('joona::user.permissions.role_name')">
					<input class="form-control" name="title" maxlength="55" type="text" value="{{$title ?? ''}}" />
				</x-joona-form-group>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<x-joona-button :caption="__('joona::common.save')" icon="check" />
	</div>
</x-joona-form>
