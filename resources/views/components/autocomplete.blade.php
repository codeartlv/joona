@if ($label)
	<label class="form-label">{{$label}}</label>
@endif

<div class="input-autocomplete" data-bind="components.autocomplete" {{$attributes}}>
	<input type="hidden" value="{{$value}}" name="{{$name}}" />
	<div class="input-group">
		<input class="form-control" type="text" />
		<span class="input-group-icon" data-role="clear">
			@icon('close')
		</span>
	</div>
</div>
