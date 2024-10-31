<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Input extends Component
{
	public function __construct(
		public ?string $name = '',
		public ?string $value = '',
		public ?string $label = '',
		public ?string $size = 'md',
		public ?string $iconPrepend = '',
		public ?string $textPrepend = '',
		public ?string $iconAppend = '',
		public ?string $textAppend = '',
		public bool $required = false,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		if ($this->iconPrepend && $this->textPrepend) {
			$this->iconPrepend = '';
		}

		if ($this->iconAppend && $this->textAppend) {
			$this->iconAppend = '';
		}

		return view('joona::components.input');
	}
}
