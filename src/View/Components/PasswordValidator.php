<?php

namespace Codeart\Joona\View\Components;

use Illuminate\View\Component;

class PasswordValidator extends Component
{
	public function __construct(
		public string $name = 'new_password',
		public string $policy = '',
		public ?string $label = '',
		public bool $required = false,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function render()
	{
		$policy = parse_password_policy($this->policy);

		return view('joona::components.password-validator', [
			'min_length' => $policy['min'] ?? 0,
		]);
	}
}
