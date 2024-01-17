<x-joona-form :action="route('joona.user.my-password-save')">
	<x-joona-dialog :caption="__('joona::user.my_password_titlebar')">
		<div data-role="form.response"></div>
		<div class="block">
			<x-joona-form-group required="true" :label="__('joona::user.current_password')">
				<input class="form-control" type="password" name="current_password" autocomplete="current-password" />
			</x-joona-form-group>

			<x-joona-form-group required="true" :label="__('joona::user.new_password')">
				<x-joona-password-validator name="password" policy="{{config('joona.admin_password_policy')}}" />
			</x-joona-form-group>
		</div>

		<x-slot name="footer">
			<x-joona-button :caption="__('joona::common.save')" icon="check" />
		</x-slot>
	</x-joona-dialog>
</x-joona-form>
