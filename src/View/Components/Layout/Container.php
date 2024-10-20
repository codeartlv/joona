<?php

namespace Codeart\Joona\View\Components\Layout;

use Illuminate\View\Component;

class Container extends Component
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.container');
	}
}
