<?php

namespace Codeart\Joona\View\Components\Autocomplete;

use Illuminate\Contracts\Support\Arrayable;

class Entry implements Arrayable
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
			'data' => [
				'id' => $this->id,
			],
			'value' => $this->label,
		];
	}
}
