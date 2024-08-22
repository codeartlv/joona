<?php

namespace Codeart\Joona\View\Components\Gallery;

readonly class Image
{
	public function __construct(
		public string $image,
		public ?string $id = null,
		public ?string $thumbnail = null,
		public ?string $title = null,
	) {
		;
	}
}
