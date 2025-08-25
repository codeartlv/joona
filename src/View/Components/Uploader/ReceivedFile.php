<?php

namespace Codeart\Joona\View\Components\Uploader;

readonly class ReceivedFile
{
	public function __construct(
		public int $id,
		public ?string $caption
	) {
		
	}
}