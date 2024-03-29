<x-form :action="route('joona.user.permission-save-role')" class="modal-content" focus="title">
	<input type="hidden" name="id" value="{{$id}}" />

	<x-dialog :caption="__('joona::user.permissions.edit_role')">
		<div data-role="form.response"></div>

		<div class="block">
			<div class="form-group required">
				<label>@lang('joona::user.permissions.role_name')</label>
				<input class="form-control" name="title" maxlength="55" type="text" value="{{$title ?? ''}}" />
			</div>
		</div>
		<x-slot name="footer">
			<x-button :caption="__('joona::common.save')" icon="check" />
		</x-slot>
	</x-dialog>
</x-form>
