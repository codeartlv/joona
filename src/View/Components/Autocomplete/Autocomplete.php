<?php

namespace Codeart\Joona\View\Components\Autocomplete;

use Illuminate\View\Component;

class Autocomplete extends Component
{
	public function __construct(
		public string $value = '',
		public string $name = '',
		public string $label = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.autocomplete');
	}
}
