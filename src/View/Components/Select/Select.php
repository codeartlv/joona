<?php

namespace Codeart\Joona\View\Components\Select;

use Illuminate\View\Component;

class Select extends Component
{
	public function __construct(
		public array $options = [],
		public bool $blank = false,
		public string $label = '',
		public string $size = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		if ($this->blank) {
			array_unshift($this->options, new Option('', ''));
		}

		return view('joona::components.select', [

		]);
	}
}
