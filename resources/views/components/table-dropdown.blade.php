@if (!empty($options))
	<div class="dropdown">
		<button class="btn btn-sm btn-outline-subtle captionless" data-bs-toggle="dropdown">
			@icon('page_info')
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			@foreach ($options as $option)
				@php
				$option += [
					'caption' => '',
					'icon' => null,
					'href' => 'javascript:;',
				];
				@endphp
				<a href="{{$option['href']}}" class="dropdown-item">
					@icon($option['icon'])
					{{$option['caption']}}
				</a>
			@endforeach
		</div>
	</div>
@endif
