<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class DropdownRadio extends Component
{
	public function __construct(
		public string $name = 'input',
		public array $options = [],
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.dropdown-radio');
	}
}
