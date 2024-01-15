<?php

namespace Codeart\Joona\View\Components\Form;

use Illuminate\View\Component;

class FormElement extends Component
{
	public function __construct(
		public string $action = '',
		public string $method = 'post',
		public ?string $focus = null,
		public string $class = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.form');
	}
}
