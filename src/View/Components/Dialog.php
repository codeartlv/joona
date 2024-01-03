<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Dialog extends Component
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
		return view('joona::components.dialog');
	}
}
