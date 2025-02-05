@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

<div {{$attributes}} data-bind="components.datepicker" class="input-group datepicker">
	<input type="text" data-role="datepicker" class="form-control" />
	<input type="hidden" data-role="value" name="{{$name}}" value="{{$value}}" />
	<a href="javascript:;" data-role="clear">@icon('clear')</a>
</div>
<div data-field="{{$name}}"></div>
