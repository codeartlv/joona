<x-joona-form :action="route('joona.user.me-save')">
	<x-joona-dialog :caption="__('joona::user.my_profile_titlebar')">
		<div data-role="form.response"></div>

		<div class="block">
			<div class="form-split">
				<span>@lang('joona::user.profile_data')</span>
			</div>

			<x-joona-form-group required="true" :label="__('joona::user.username')">
				<input class="form-control" type="text" readonly disabled maxlength="25" value="admin" />
			</x-joona-form-group>

			<div class="row">
				<div class="col-12 col-md-6">
					<x-joona-form-group required="true" :label="__('joona::user.first_name')">
						<input class="form-control" type="text" name="first_name" autocomplete="name" maxlength="55" value="{{$first_name}}" />
					</x-joona-form-group>
				</div>
				<div class="col-12 col-md-6">
					<x-joona-form-group required="true" :label="__('joona::user.last_name')">
						<input class="form-control" type="text" name="last_name" autocomplete="family-name" maxlength="55" value="{{$last_name}}" />
					</x-joona-form-group>
				</div>
			</div>

			<x-joona-form-group class="mb-0" required="true" :label="__('joona::user.email')">
				<input class="form-control" type="email" name="email" autocomplete="email" maxlength="128" value="{{$email}}" />
			</x-joona-form-group>
		</div>

		<x-slot name="footer">
			<section>
				<x-joona-button type="button" role="outline-secondary" :caption="__('joona::user.change_password')" icon="password" :attr="['data-bind' => 'admin.change-my-password']" />
			</section>
			<section>
				<x-joona-button :caption="__('joona::common.save')" icon="check" />
			</section>
		</x-slot>
	</x-joona-dialog>
</x-joona-form>
