<?php

namespace Codeart\Joona\View\Components\Uploader\File;

use Codeart\Joona\Enums\FileCategory;
use Codeart\Joona\View\Components\Uploader\UploadedFile;

final readonly class Image extends UploadedFile
{
	public function __construct(
		public mixed $id = null,
		public ?string $caption = null,
		public ?string $filename = null,
		public ?string $thumbnailUrl = null,
		public ?string $imageUrl = null,
		public array $properties = [],
	)
	{
		
	}

	public function toArray()
	{
		return [
			'id' => $this->id,
			'type' => FileCategory::IMAGE->value,
			'caption' => $this->caption,
			'filename' => $this->filename,
			'thumbnail' => $this->thumbnailUrl,
			'image' => $this->imageUrl,
			'properties' => $this->properties,
		];
	}
}
