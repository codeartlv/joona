<div x-data="datepicker" data-timepicker="{{$timepicker?'true':'false'}}" data-mindate="{{$minDate?$minDate->format('Y-m-d H:i:s'):''}}" class="input-group">
	<input type="text" data-role="control" class="form-control" />
	<input type="hidden" data-role="value" name="{{$name}}" value="{{$value}}" />
</div>
