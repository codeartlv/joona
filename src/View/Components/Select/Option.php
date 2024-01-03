<?php

namespace Codeart\Joona\View\Components\Select;

use Illuminate\Contracts\Support\Arrayable;

class Option implements Arrayable
{
	public function __construct(public $value, public string $label, public bool $selected = false, public bool $disabled = false)
	{
	}

	public function toArray()
	{
		return [
			'value' => $this->value,
			'label' => $this->label,
			'selected' => $this->selected,
			'disabled' => $this->disabled,
		];
	}
}
