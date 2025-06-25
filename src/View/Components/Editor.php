<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Editor extends Component
{
	public function __construct(
		public ?string $name = '',
		public ?string $label = '',
		public array $content = [],
		public bool $required = false,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.editor');
	}
}
