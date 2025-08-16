<?php

namespace Codeart\Joona\View\Components\Uploader;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

abstract readonly class UploadedFile implements JsonSerializable, Arrayable
{
	public function __construct(
		public mixed $id = null,
		public ?string $filename = null,
		public ?string $caption = null,
	)
	{
		
	}

	public function jsonSerialize(): array
	{
		return $this->toArray();
	}
}
