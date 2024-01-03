@php
$id = 'chk'.uniqid();
@endphp

@if ($type == 'button')
	<input type="checkbox" {{$attributes}} {{$checked?'checked':''}} value="{{$value}}" id="{{$id}}" class="btn-check" autocomplete="off">
	<label class="btn btn-{{$role}}" for="{{$id}}">{{$label}}</label>
@else
	<div class="{{$label ? 'form-check':''}} {{$type=='switch' ? 'form-switch':''}}">
		<input class="form-check-input" type="{{$type}}" {{$attributes}} {{$checked?'checked':''}} value="{{$value}}" id="{{$id}}" {{$type == 'switch' ? 'role="switch"':''}}>
		@if ($label)
			<label class="form-check-label" for="{{$id}}">
				{{$label}}
			</label>
		@endif
	</div>
@endif

