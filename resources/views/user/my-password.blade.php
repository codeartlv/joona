<x-form :action="route('joona.user.my-password-save')">
	<x-dialog :caption="__('joona::user.my_password_titlebar')">
		<div data-role="form.response"></div>
		<div class="block">
			<div class="form-group required">
				<label>@lang('joona::user.current_password')</label>
				<input class="form-control" type="password" name="current_password" autocomplete="current-password" />
			</div>

			<div class="form-group required">
				<label>@lang('joona::user.new_password')</label>
				<x-password-validator name="password" policy="{{config('joona.admin_password_policy')}}" />
			</div>
		</div>

		<x-slot name="footer">
			<x-button :caption="__('joona::common.save')" icon="check" />
		</x-slot>
	</x-dialog>
</x-form>
