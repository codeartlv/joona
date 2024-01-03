<?php

namespace Codeart\Joona\View\Components\Form;

use Illuminate\View\Component;

class FormElement extends Component
{
	public function __construct(
		public string $method = 'post',
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
