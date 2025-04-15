<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class DataTable extends Component
{
	public function __construct(
		public ?string $sortable = null,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.table', [
			'sortable' => $this->sortable,
		]);
	}
}
