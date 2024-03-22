<form x-data="form" action="{{$action}}" method="{{$method}}" class="{{$class}}" data-focus="{{$focus}}">
	@csrf
	{{ $slot }}
</form>
