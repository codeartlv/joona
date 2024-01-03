<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Accordion extends Component
{
	public function __construct(
		public array $items = [],
		public bool $autocollapse = true,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		foreach ($this->items as $i => $item) {
			$item += ['', '', 0];
			$this->items[$i] = $item;
		}

		return view('joona::components.accordion');
	}
}
