<x-joona-form :action="route('joona.user.permission-save-role')" class="modal-content" focus="title">
	<input type="hidden" name="id" value="{{$id}}" />

	<x-joona-dialog :caption="__('joona::user.permissions.edit_role')">
		<div data-role="form.response"></div>

		<div class="block">
			<x-joona-form-group required="true" :label="__('joona::user.permissions.role_name')">
				<input class="form-control" name="title" maxlength="55" type="text" value="{{$title ?? ''}}" />
			</x-joona-form-group>
		</div>
		<x-slot name="footer">
			<x-joona-button :caption="__('joona::common.save')" icon="check" />
		</x-slot>
	</x-joona-dialog>
</x-joona-form>
