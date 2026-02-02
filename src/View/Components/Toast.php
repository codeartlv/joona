<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Toast extends Component
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.toast'); 
	}
}
