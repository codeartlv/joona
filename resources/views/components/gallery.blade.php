<ol class="gallery-thumbnails {{$class}}" data-bind="components.gallery" data-sortable="{{$sortable ? 'true':'false'}}">
	@php
		$i=0;
	@endphp
	@foreach ($images as $image)
		<li data-trigger="gallery.open" data-index="{{$i}}" data-role="gallery.pic">
			<input type="hidden" name="{{$name}}[]" value="{{$image->id}}" />
			<figure style="background-image:url('{{$image->thumbnail ?: $image->image}}')" data-role="gallery.pic"></figure>
		</li>
		@php
			$i++;
		@endphp
	@endforeach
	<script data-role="gallery.images" type="application/ld+json">@json($images)</script>
</ol>
