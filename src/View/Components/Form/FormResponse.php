<?php

namespace Codeart\Joona\View\Components\Form;

use Codeart\Joona\Contracts\Result;

class FormResponse extends Result
{
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
	public function setError($message, $field = null): Result
	{
		return parent::setError($message, $field ?? '*');
	}
}
