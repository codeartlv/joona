<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Multiselect extends Component
{
	public function __construct(
		public array $options = [],
		public string $label = '',
		public string $name = '',
		public bool $required = false,
	) {

	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.multiselect');
	}
}
