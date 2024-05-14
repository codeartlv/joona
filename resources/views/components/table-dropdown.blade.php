@if (!empty($options))
	<div class="dropdown">
		<button class="btn btn-sm btn-outline-subtle captionless" data-bs-toggle="dropdown">
			@icon('page_info')
		</button>
		<div class="dropdown-menu dropdown-menu-right">
			@foreach ($options as $option)
				@php

				if ($option == '-') {
					echo '<li><hr class="dropdown-divider"></li>';
					continue;
				}

				$option += [
					'caption' => '',
					'icon' => null,
					'href' => 'javascript:;',
					'attributes' => [],
				];
				@endphp
				<a href="{{$option['href']}}" @attributes($option['attributes']) class="dropdown-item">
					@icon($option['icon'])
					{{$option['caption']}}
				</a>
			@endforeach
		</div>
	</div>
@endif
