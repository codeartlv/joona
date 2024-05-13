<?php

namespace Codeart\Joona\View\Components\Form;

use Codeart\Joona\Contracts\Result;

class FormResponse extends Result
{
	/**
	 * Required JS actions
	 *
	 * @var mixed[]
	 */
	protected $actions = [];

	/**
	 * @inheritDoc
	 */
	public function getResult(): array
	{
		return [
			'status' => $this->result ? 'success' : 'error',
			'fields' => $this->errors,
			'message' => $this->message,
			'actions' => $this->actions,
			'data' => $this->data,
		];
	}

	/**
	 * @inheritDoc
	 */
	public function setError($message, $field = null): static
	{
		return parent::setError($message, $field ?? '*');
	}

	/**
	 * Sets required action
	 *
	 * @param string $action
	 * @param mixed $value
	 * @return boolean
	 */
	public function setAction(string $action, $value): bool
	{
		$actions = explode(',', $action);

		foreach ($actions as $action) {
			$this->actions[$action] = $value;
		}

		return true;
	}
}
