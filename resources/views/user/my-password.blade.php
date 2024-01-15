<x-joona-form :action="route('joona.user.my-password-save')" class="modal-content">
	<div class="modal-header">
		<h5 class="modal-title">@lang('joona::user.my_password_titlebar')</h5>
		<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
			@icon('close')
		</button>
	</div>
	<div class="modal-body">
		<div class="modal-inner">
			<div data-role="form.response"></div>

			<div class="block">
				<x-joona-form-group required="true" :label="__('joona::user.current_password')">
					<input class="form-control" type="password" name="current_password" autocomplete="current-password" />
				</x-joona-form-group>

				<x-joona-form-group required="true" :label="__('joona::user.new_password')">
					<x-joona-password-validator name="password" policy="{{config('joona.admin_password_policy')}}" />
				</x-joona-form-group>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<x-joona-button :caption="__('joona::common.save')" icon="check" />
	</div>
</x-joona-form>
