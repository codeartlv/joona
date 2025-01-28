<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class TableBulkOptions extends Component
{
	public function __construct(
		public ?string $tableid = '',
		public ?string $url = '',
		public array $options = [],
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.table-bulk-options');
	}
}
