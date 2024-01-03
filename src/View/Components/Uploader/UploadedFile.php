<?php

namespace Codeart\Joona\View\Components\Uploader;

use Illuminate\Contracts\Support\Arrayable;

class UploadedFile implements Arrayable
{
	public bool $error = false;
	public bool $locked = false;
	public ?string $message = null;
	public ?string $type = null;
	public $id = null;
	public ?string $url = null;

	public function toArray()
	{
		return [
			'error' => $this->error,
			'message' => $this->message,
			'id' => $this->id,
			'url' => $this->url,
			'type' => $this->type,
		];
	}
}
