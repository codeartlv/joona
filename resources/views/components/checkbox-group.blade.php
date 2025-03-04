@if ($label)
	<label class="form-label {{$required ? 'required':''}}">{{$label}}</label>
@endif

<div class="btn-group" role="group">
    @foreach ($options as $option)
        @php
            $id = 'chk'.uniqid();
        @endphp
        <input type="{{$type}}" class="btn-check" name="{{$name}}" id="{{$id}}" value="{{$option->value}}" autocomplete="off" {{$option->selected ? 'checked':''}}>
        <label class="btn btn-outline-primary" for="{{$id}}">{{$option->label}}</label>
    @endforeach
</div>

