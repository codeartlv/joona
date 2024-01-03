@if ($label)
	<label class="form-label">{{$label}}</label>
@endif

<div class="input-group input-autocomplete" data-bind="components.autocomplete" {{$attributes}}>
	<input class="form-control" type="text" />
	<input type="hidden" value="{{$value}}" name="{{$name}}" />
	<span class="input-group-icon" data-role="clear">
		@icon('close')
	</span>
</div>
