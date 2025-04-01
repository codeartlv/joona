<div class="navbar bottom">
	<ul class="navbar-nav">
		<li class="nav-item">
			@choice('joona::common.records', $total, [$total])
		</li>
	</ul>
	<ul class="navbar-nav">
		<li class="nav-item">
			<x-paginator :total="$total" :size="$size" />
		</li>
	</ul>
</div>