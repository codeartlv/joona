<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Checkbox extends Component
{
	public function __construct(
		public string $label = '',
		public string $type = 'checkbox',
		public string $value = '1',
		public string $role = 'primary',
		public bool $checked = false,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.checkbox');
	}
}
