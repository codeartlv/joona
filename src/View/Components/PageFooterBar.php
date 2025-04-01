<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class PageFooterBar extends Component
{
	public function __construct(
		public int $total = 0,
		public int $size = 25,
	) {
	
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.page-footer-bar', [
			'total' => $this->total,
			'size' => $this->size,
		]);
	}
}
