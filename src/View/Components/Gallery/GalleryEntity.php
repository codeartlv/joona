<?php

namespace Codeart\Joona\View\Components\Gallery;

use Codeart\Joona\Enums\GalleryEntityType;

readonly class GalleryEntity
{
	public function __construct(
		public string $url,
		public GalleryEntityType $type,
		public ?string $id = null,
		public ?string $thumbnail = null,
		public ?string $title = null,
	) {
		;
	}
}
