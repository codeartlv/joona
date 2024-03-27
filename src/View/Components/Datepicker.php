<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class Datepicker extends Component
{
	public function __construct(
		public ?string $value = '',
		public ?string $name = 'date',
		public ?bool $timepicker = false,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.datepicker', [

		]);
	}
}
