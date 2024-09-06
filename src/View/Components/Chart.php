<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Chart extends Component
{
	public function __construct(
		public array $data = [],
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.chart', [
			'data' => $this->data,
		]);
	}
}
