<?php

namespace Codeart\Joona\View\Components\Tag;

use Illuminate\View\Component;

class Tags extends Component
{
	public function __construct(
		public ?string $label = '',
		public ?string $searchUrl,
		public array $value = [],
		public bool $required = false,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.tags');
	}
}
