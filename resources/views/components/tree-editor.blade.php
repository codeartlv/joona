<div class="tree-editor" data-bind="components.tree-editor" data-selected="{{$selected}}" data-sortable="{{$sortable ? 'true':'false'}}" data-depth="{{$depth}}" data-editRoute="{{$editRoute}}" data-sortRoute="{{$sortRoute}}" data-delRoute="{{$deleteRoute}}">
	<template data-role="node">
		<li>
			<a href="javascript:;">
				{{-- <img class="tree-editor__icon" data-field="icon" data-attr="src" /> --}}
				<em data-field="title"></em>
				<span class="tree-editor__edit">
					<span data-action="edit-node">
						@icon('edit')
					</spamn>
				</span>
				<span class="tree-editor__delete">
					<span href="javascript:;" data-action="delete-node">
						@icon('delete')
					</span>
				</span>
			</a>
		</li>
	</template>
	<template data-role="section">
		<section class="card" data-level="-1" data-role="level">
			<div class="card-header">
				<x-button type="button" class="btn-sm" role="outline-primary" data-action="add-node" icon="docs_add_on" :caption="__('joona::common.add')" />
			</div>
			<div class="card-body">
				<ul data-role="nodes">
				</ul>
				<div class="alert alert-info alert-sm">
					{{__('joona::common.no_nodes_in_list')}}
				</div>
			</div>
		</section>
	</template>
	<script data-role="tree-editor.data" type="application/ld+json">@json($rows)</script>
</div>
