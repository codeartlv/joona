<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class TreeEditor extends Component
{
	public function __construct(
		public bool $sortable = true,
		public int $depth = 3,
		public int $selected = 0,
		public ?string $editRoute = '',
		public ?string $sortRoute = '',
		public ?string $deleteRoute = '',
		public array $rows = [],
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$groupped = [];

		foreach ($this->rows as $item) {
			$groupped[(string) $item->parentId][] = $item;
		}

		$get_tree = function ($rows) use (&$get_tree, $groupped) {
			$result = [];

			foreach ($rows as $row) {
				$result[] = [
					'id' => (int) $row->id,
					'title' => $row->title,
					'parent_id' => (int) $row->parentId,
					'childs' => $get_tree(!empty($groupped[$row->id]) ? $groupped[$row->id] : []),
					'data' => $row->data,
				];
			}

			return $result;
		};

		$this->rows = !empty($groupped['']) ? $get_tree($groupped['']) : [];

		return view('joona::components.tree-editor');
	}
}
