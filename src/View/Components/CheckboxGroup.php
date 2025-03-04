<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class CheckboxGroup extends Component
{
	public function __construct(
		public string $name = '',
		public bool $required = false,
		public string $label = '',
		public string $type = 'checkbox',
		public array $options = [],
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
        return view('joona::components.checkbox-group');
	}
}
