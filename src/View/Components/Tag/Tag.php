<?php

namespace Codeart\Joona\View\Components\Tag;

use Illuminate\Contracts\Support\Arrayable;

class Tag implements Arrayable, \JsonSerializable
{
	private $id;
	private $label;

	public function __construct($id, string $label)
	{
		$this->id = $id;
		$this->label = $label;
	}

	public function toArray()
	{
		return [
			'id' => $this->id,
			'value' => $this->label,
		];
	}

	public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
