@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

<div data-bind="components.editor" data-name="content" {{ $attributes }}>
	<script data-role="data" type="application/ld+json">@json($content)</script>	
</div>