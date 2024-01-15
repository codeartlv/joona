<?php

namespace Codeart\Joona\View\Components\Form;

use Illuminate\View\Component;

class Button extends Component
{
	public function __construct(
		public string $type = 'submit',
		public string $role = 'primary',
		public string $caption = '',
		public string $icon = '',
		public bool $block = false,
		public array $attr = [],
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$classes = [
			'btn',
			'btn-'.$this->role
		];

		if ($this->block) {
			$classes[] = 'btn-block';
		}

		$custom_attr = $this->attr + [
			'type' => $this->type,
			'class' => implode(' ', $classes),
		];

		return view('joona::components.button', [
			'custom_attr' => $custom_attr,
		]);
	}
}
