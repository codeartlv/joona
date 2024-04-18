<x-form action="{{route('joona.user.me-save')}}">
	<x-dialog :caption="__('joona::user.my_profile_titlebar')">

		<div class="block">
			<x-form-section-heading :label="__('joona::user.profile_data')"/>

			<div class="form-group">
				<x-input :label="__('joona::user.email')" required="true" type="email" name="email" autocomplete="email" maxlength="128" :value="$email" />
			</div>

			<div class="row">
				<div class="col-12 col-md-6">
					<div class="form-group">
						<x-input :label="__('joona::user.first_name')" required="true" type="text" name="first_name" autocomplete="name" maxlength="55" :value="$first_name" />
					</div>
				</div>
				<div class="col-12 col-md-6">
					<div class="form-group">
						<x-input :label="__('joona::user.last_name')" required="true" type="text" name="last_name" autocomplete="family-name" maxlength="55" :value="$last_name" />
					</div>
				</div>
			</div>
		</div>

		<x-slot name="footer">
			<section>
				<x-button type="button" role="outline-secondary" :caption="__('joona::user.change_password')" icon="password" data-bind="admin.changeMyPassword" />
			</section>
			<section>
				<x-button :caption="__('joona::common.save')" icon="done" />
			</section>
		</x-slot>
	</x-dialog>
</x-form>
