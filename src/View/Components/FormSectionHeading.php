<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class FormSectionHeading extends Component
{
	public function __construct(
		public ?string $label = '',
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.form-section-heading');
	}
}
