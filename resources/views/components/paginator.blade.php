<nav>
	<ul class="pagination">
		<li class="page-item page-item-first page-item-control {{ $current_page == 1 ? 'disabled' : '' }}">
			<a class="page-link" href="{{ request()->fullUrlWithQuery([$param => $current_page - 1]) }}">
				@icon('navigate_before')
			</a>
		</li>

		@foreach ($pages as $page)
			<li class="page-item page-item-number">
				<a data-page="{{$page['number']}}" class="page-link {{$page['active'] ? 'active':''}}" href="{{request()->fullUrlWithQuery([$param => $page['number']])}}">
					{{$page['number']}}
				</a>
			</li>
		@endforeach

		<li class="page-item page-item-last page-item-control {{ $current_page == $total_pages ? 'disabled' : '' }}">
			<a class="page-link" href="{{ request()->fullUrlWithQuery([$param => $current_page + 1]) }}">
				@icon('navigate_next')
			</a>
		</li>
	</ul>
</nav>
