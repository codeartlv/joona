<nav>
	<ul class="pagination" data-role="pagination">
		<li class="page-item page-item-first page-item-control {{ empty($back_link) ? 'disabled' : '' }}"  @attributes($back_link)>
			<a class="page-link" data-page="{{$current_page - 1}}" >
				@icon('navigate_before')
			</a>
		</li>

		@foreach ($pages as $page)
			<li class="page-item page-item-number">
				<a data-page="{{$page['number']}}" class="page-link {{$page['active'] ? 'active':''}}" @attributes($page['attr']) >
					{{$page['number']}}
				</a>
			</li>
		@endforeach

		<li class="page-item page-item-last page-item-control {{ empty($forward_link) ? 'disabled' : '' }}" @attributes($forward_link)>
			<a data-page="{{$current_page + 1}}" class="page-link">
				@icon('navigate_next')
			</a>
		</li>
	</ul>
</nav>
