<?php

namespace Codeart\Joona\View\Components\Layout;

use Illuminate\View\Component;

class Content extends Component
{
	public function __construct(
		public string $title = '',
	) {

	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.layout');
	}
}
