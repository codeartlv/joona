@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

@php
	$tagValueId = 'tags_'.uniqid();
@endphp
<input data-bind="components.tags" {{ $attributes }} class="form-control form-tag-autocomplete" type="text" data-valuesDataId="{{$tagValueId}}" />
<script data-id="{{$tagValueId}}" type="application/ld+json">@json($value)</script>
