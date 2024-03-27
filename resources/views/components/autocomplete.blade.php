<div class="input-group input-autocomplete" x-data="autocomplete" data-route="{{$route}}">
	<input class="form-control" type="text" />
	<input type="hidden" value="{{$value}}" name="{{$name}}" />
	<span class="input-group-icon" data-role="clear">
		@icon('close')
	</span>
</div>
