<?php

namespace Codeart\Joona\View\Components\Uploader;

use Illuminate\View\Component;

class Uploader extends Component
{
	public function __construct(
		public string $name = 'files',
		public string $class = 'default',
		public string $uploadRoute = '',
		public string $deleteRoute = '',
		public string $submitButton = '',
		public int $limit = 1,
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
