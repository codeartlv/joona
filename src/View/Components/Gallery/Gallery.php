<?php

namespace Codeart\Joona\View\Components\Gallery;

use Illuminate\View\Component;

class Gallery extends Component
{
	public function __construct(
		public array $images = [],
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
		return view('joona::components.gallery');
	}
}
