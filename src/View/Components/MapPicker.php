<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class MapPicker extends Component
{
	public function __construct(
		public ?string $class = '',
		public ?string $name = '',
		public ?string $value = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.map-picker');
	}
}
