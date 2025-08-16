<?php

namespace Codeart\Joona\View\Components\Uploader;

use Codeart\Joona\Enums\UploadStatus;
use Illuminate\Contracts\Support\Arrayable;

readonly class UploadResponse implements Arrayable
{
	public function __construct(
		public UploadStatus $status,
		public ?UploadedFile $file = null,
		public ?string $message = null,
	)
	{
		;
	}

	public function toArray()
	{
		return [
			'file' => $this->file?->toArray(),
			'error' => $this->status === UploadStatus::FAILED,
			'message' => $this->message,
		];
	}
}
