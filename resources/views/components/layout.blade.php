@section('page_title', $title)

@isset($sidebar)
	@include('joona::components.layout.layout-sidebar')
@else
	@include('joona::components.layout.layout-simple')
@endif

