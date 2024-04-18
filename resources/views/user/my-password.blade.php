<x-form action="{{route('joona.user.my-password-save')}}">
	<x-dialog :caption="__('joona::user.my_password_titlebar')">
		<div class="block">
			<div class="form-group">
				<x-input :label="__('joona::user.current_password')" required="true" type="password" name="current_password" autocomplete="current-password" />
			</div>

			<div class="form-group">
				<x-password-validator name="password" required="true" :label="__('joona::user.new_password')" policy="{{config('joona.admin_password_policy')}}" />
			</div>
		</div>

		<x-slot name="footer">
			<x-button :caption="__('joona::common.save')" icon="check" />
		</x-slot>
	</x-dialog>
</x-form>
