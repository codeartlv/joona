<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Button extends Component
{
	public function __construct(
		public string $role = 'primary',
		public string $caption = '',
		public string $icon = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.button');
	}
}
