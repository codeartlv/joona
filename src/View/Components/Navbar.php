<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Navbar extends Component
{
	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.navbar');
	}
}
