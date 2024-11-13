<?php

namespace Codeart\Joona\View\Components\Gallery;

use Illuminate\View\Component;

class Gallery extends Component
{
	public function __construct(
		public array $items = [],
		public ?string $name = 'gallery',
		public ?string $class = '',
		public bool $sortable = false,
		public bool $loop = false,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$this->items = array_values($this->items);

		return view('joona::components.gallery');
	}
}
