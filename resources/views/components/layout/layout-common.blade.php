@section('controls')
	@isset($controls)
		{{$controls}}
	@endif
@endsection

@section('main')
	{{$slot}}
@endsection

@section('foot')
	@isset($footer)
		{{$footer}}
	@endif
@endsection
