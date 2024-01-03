<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
	public function __construct(
		public string $role = 'danger',
		public string $class = '',
	) {

	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.alert');
	}
}
