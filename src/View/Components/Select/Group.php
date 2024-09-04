<?php

namespace Codeart\Joona\View\Components\Select;

use Illuminate\Contracts\Support\Arrayable;

class Group implements Arrayable
{
	/**
	 * @var Option[] Group elements
	 */
	private $options = [];

	public function __construct(
		public $value,
		public string $label,
		public bool $selectable = false,
		public bool $selected = false,
		public bool $disabled = false
	) {
		;
	}

	public function add(Option $option)
	{
		$this->options[] = $option;
	}

	/**
	 * Return group options
	 *
	 * @return Option[]
	 */
	public function options(): array
	{
		return $this->options;
	}

	public function toArray()
	{
		return [
			'value' => $this->value,
			'label' => $this->label,
			'selected' => $this->selected,
			'selectable' => $this->selectable,
			'disabled' => $this->disabled,
			'childs' => array_map(fn($option) => $option->toArray(), $this->options),
		];
	}
}
