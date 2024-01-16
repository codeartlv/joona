<?php

namespace Codeart\Joona\View\Components\Form;

use Illuminate\View\Component;

class Uploader extends Component
{
	public function __construct(
		public string $name = 'files',
		public string $class = 'default',
		public string $uploadroute = '',
		public string $deleteroute = '',
		public string $submitbtn = '',
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
