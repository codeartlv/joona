<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Offcanvas extends Component
{
	public function __construct(
		public string $caption = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.offcanvas');
	}
}
