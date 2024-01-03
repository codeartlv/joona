<form data-bind="components.form" method="{{$method}}" {{$attributes}}>
	@csrf
	{{ $slot }}
</form>
