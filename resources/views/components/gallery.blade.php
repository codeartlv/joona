<ol class="gallery-thumbnails {{$class}}" data-bind="components.gallery" data-sortable="{{$sortable ? 'true':'false'}}">
	@php
		$i=0;
	@endphp
	@foreach ($items as $item)
		<li data-trigger="gallery.open" data-index="{{$i}}" data-role="gallery.pic">
			<input type="hidden" name="{{$name}}[]" value="{{$item->id}}" />
			<figure style="background-image:url('{{$item->thumbnail ?: $item->url}}')" data-role="gallery.pic"></figure>
		</li>
		@php
			$i++;
		@endphp
	@endforeach
	<script data-role="gallery.items" type="application/ld+json">@json($items)</script>
</ol>
