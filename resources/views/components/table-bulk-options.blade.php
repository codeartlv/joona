<nav class="navbar navbar-inline navbar-table-bulk" data-bind="components.table-bulk-options" {{$attributes}}>
	<div class="container-fluid">
		<form action="{{$url}}" method="POST">
			@csrf
			<div class="navbar-inline__content">
				<section>
					@lang('joona::common.with_selected'):
				</section>
				<section>
					<x-select name="action" data-role="options" disabled :options="$options" blank="true" size="sm" />
				</section>
				<x-button type="button" class="btn-sm" data-role="submit" caption="OK" />
			</div>
		</form>
	</div>
</nav>
