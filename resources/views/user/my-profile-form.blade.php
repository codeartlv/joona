<x-form :action="route('joona.user.me-save')">
	<x-dialog :caption="__('joona::user.my_profile_titlebar')">
		<div data-role="form.response"></div>

		<div class="block">
			<div class="form-split">
				<span>@lang('joona::user.profile_data')</span>
			</div>

			<div class="form-group">
				<label class="required">@lang('joona::user.email')</label>
				<input class="form-control" type="email" name="email" autocomplete="email" maxlength="128" value="{{$email}}" />
			</div>

			<div class="row">
				<div class="col-12 col-md-6">
					<div class="form-group">
						<label>@lang('joona::user.first_name')</label>
						<input class="form-control" type="text" name="first_name" autocomplete="name" maxlength="55" value="{{$first_name}}" />
					</div>
				</div>
				<div class="col-12 col-md-6">
					<div class="form-group">
						<label>@lang('joona::user.last_name')</label>
						<input class="form-control" type="text" name="last_name" autocomplete="family-name" maxlength="55" value="{{$last_name}}" />
					</div>
				</div>
			</div>
		</div>

		<x-slot name="footer">
			<section>
				<x-button type="button" role="outline-secondary" :caption="__('joona::user.change_password')" icon="password" :attr="['x-data' => 'adminChangeMyPpassword']" />
			</section>
			<section>
				<x-button :caption="__('joona::common.save')" icon="done" />
			</section>
		</x-slot>
	</x-dialog>
</x-form>
