@if ($label)
	<label class="form-label">{{$label}}</label>
@endif

<div {{$attributes}} data-bind="components.datepicker" class="input-group">
	<input type="text" data-role="datepicker" class="form-control" />
	<input type="hidden" data-role="value" name="{{$name}}" value="{{$value}}" />
</div>
