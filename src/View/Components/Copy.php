<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Copy extends Component
{
	public function __construct(
		public string $text = '',
	) {

	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.copy');
	}
}
