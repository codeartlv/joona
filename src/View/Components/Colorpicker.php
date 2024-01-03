<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Colorpicker extends Component
{
	public function __construct(
		public ?string $name = '',
		public ?string $label = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.colorpicker');
	}
}
