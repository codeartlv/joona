<?php

namespace Codeart\Joona\View\Components\Uploader;

use Illuminate\View\Component;

class Uploader extends Component
{
	public function __construct(
		public string $class = 'default',
		public array $files = [],
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		return view('joona::components.uploader', [

		]);
	}
}
