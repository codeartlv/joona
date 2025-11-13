<?php

namespace Codeart\Joona\View\Components\Uploader\File;

use Codeart\Joona\Enums\FileCategory;
use Codeart\Joona\View\Components\Uploader\UploadedFile;

final readonly class Document extends UploadedFile
{
	public function __construct(
		public mixed $id = null,
		public ?string $filename = null,
		public ?string $caption = null,
		public array $properties = [],
	)
	{
		
	}

	public function toArray()
	{
		$extension = strtolower(pathinfo($this->filename, PATHINFO_EXTENSION) ?? '');

		return [
			'id' => $this->id,
			'type' => FileCategory::DOCUMENT->value,
			'caption' => $this->caption,
			'filename' => $this->filename,
			'extension' => $extension,
			'properties' => $this->properties,
		];
	}
}
