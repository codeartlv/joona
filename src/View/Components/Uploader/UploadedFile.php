<?php

namespace Codeart\Joona\View\Components\Uploader;

use Illuminate\Contracts\Support\Arrayable;

class UploadedFile implements Arrayable
{
	public $id = null;
	public ?string $filename = null;
	public ?string $thumbnail = null;
	public bool $error = false;
	public bool $locked = false;
	public ?string $message = null;

	public function toArray()
	{
		return [
			'id' => $this->id,
			'filename' => $this->filename,
			'thumbnail' => $this->thumbnail,
			'error' => $this->error,
			'message' => $this->message,
		];
	}
}
