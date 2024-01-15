<?php

namespace Codeart\Joona\View\Components\Form;

use Illuminate\View\Component;

class Group extends Component
{
	public function __construct(
		public bool $required = false,
		public string $label = 'Label',
		public string $class = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.form-group');
	}
}
