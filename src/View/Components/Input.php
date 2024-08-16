<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
	public function __construct(
		public ?string $name = '',
		public ?string $value = '',
		public ?string $label = '',
		public ?string $size = 'md',
		public bool $required = false,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.input');
	}
}
