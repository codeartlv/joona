@if (!empty($menu))
	<ul class="nav main-nav">
		@foreach ($menu as $key => $top_item)
			@php
			$total_badge = $top_item['badge'] ?? 0;
			@endphp
			<li class="nav-item">
				@if (!empty($top_item['childs']))
					@php
					foreach ($top_item['childs'] as $child_item) {
						$total_badge += $child_item['badge'] ?? 0;
					}
					@endphp

					<a class="collapsed {{$top_item['active'] ? 'active':''}}" data-bs-toggle="collapse" href="#menu-{{$key}}">
						@icon($top_item['icon'])
						<span>{{__($top_item['caption'])}}</span>
						@if ($total_badge > 0)
							<span class="badge bg-primary">{{$total_badge}}</span>
						@endif
					</a>
					<div class="collapse {{$top_item['active'] ? 'show':''}}" id="menu-{{$key}}">
						<ul>
							@foreach ($top_item['childs'] as $child_item)
								<li class="nav-subitem">
									<a class="{{$child_item['active'] ? 'active':''}}" href="{{$child_item['url']}}">
										@if ($child_item['icon'] ?? '')
											@icon($child_item['icon'])
										@endif

										<span>{{__($child_item['caption'])}}</span>
										@if ($child_item['badge'] ?? 0)
											<span class="badge bg-secondary">{{$child_item['badge']}}</span>
										@endif
									</a>
								</li>
							@endforeach
						</ul>
					</div>
				@else
					@if ($top_item['url'])
						<a href="{{$top_item['url']}}" class="{{$top_item['active'] ? 'active':''}}">
							@icon($top_item['icon'])
							<span>{{__($top_item['caption'])}}</span>
							@if ($total_badge > 0)
								<span class="badge bg-primary">{{$total_badge}}</span>
							@endif
						</a>
					@endif
				@endif
			</li>
		@endforeach
	</ul>
@endif

