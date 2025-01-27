@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

<div class="text-editor" data-bind="components.text-editor">
	<div class="pell" data-role="text-editor"></div>
	<textarea name="{{$name}}" class="form-control">{{$value}}</textarea>
</div>
