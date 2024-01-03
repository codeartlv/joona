<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Range extends Component
{
	public function __construct(
		public ?string $name = '',
		public ?string $value = '',
		public ?string $label = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.range');
	}
}
