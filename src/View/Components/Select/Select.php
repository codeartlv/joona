<?php

namespace Codeart\Joona\View\Components\Select;

use Illuminate\View\Component;

class Select extends Component
{
	public function __construct(
		public ?string $name = null,
		public array $attr = [],
		public array $options = [],
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.select', [

		]);
	}
}
