<?php

namespace Codeart\Joona\Contracts;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Payload that contains response from various objects
 *
 * @package Codeart\Joona\Contracts
 */
class Result implements Arrayable
{
	/**
	 * Form errors
	 *
	 * @var mixed[]
	 */
	protected $errors = [];

	/**
	 * Required JS actions
	 *
	 * @var mixed[]
	 */
	protected $actions = [];

	/**
	 * Response global result
	 *
	 * @var bool
	 */
	protected $result = true;

	/**
	 * Success message
	 *
	 * @var mixed[]
	 */
	protected $message = [];

	/**
	 * Additional data
	 *
	 * @var mixed[]
	 */
	protected $data = [];

	/**
	 * Sets error
	 *
	 * @param string $message Error message
	 * @param string $field Field name
	 * @return Result
	 */
	public function setError($message, $field = null): self
	{
		if (!is_array($this->errors)) {
			$this->errors = [];
		};

		$this->errors[$field][] = $message;
		$this->result = false;

		return $this;
	}

	/**
	 * Whether error exists
	 *
	 * @param string $field
	 * @return boolean
	 */
	public function isError(string $field): bool
	{
		return isset($this->errors[$field]);
	}

	/**
	 * Attach additional data
	 *
	 * @param mixed[] $data
	 * @return Result
	 */
	public function setData(array $data): self
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * Attaches data to object
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return Result
	 */
	public function addData(string $key, $value)
	{
		$this->data[$key] = $value;
		return $this;
	}

	/**
	 * Returns attached data
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getData($key)
	{
		return $this->data[$key] ?? null;
	}

	/**
	 * Returns attached data
	 *
	 * @return mixed
	 */
	public function getAllData()
	{
		return $this->data;
	}

	/**
	 * Wrapper to getData()
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->getData($key);
	}

	/**
	 * Shortcut to getData() function, allows retreive stored values using getValue syntax
	 *
	 * @param string $name Object access key
	 * @param mixed[] $arguments Function arguments
	 *
	 * @return  mixed				Stored value
	 */
	public function __call(string $name, $arguments)
	{
		$key = strtolower(ltrim($name, 'get'));
		return $this->getData($key);
	}

	/**
	 * Sets success message
	 *
	 * @param string$message
	 * @return boolean
	 */
	public function setSuccess(string $message): bool
	{
		$this->message[] = $message;
		$this->result = true;

		return true;
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

	/**
	 * Returns form result
	 *
	 * @return mixed[]
	 */
	public function getResult(): array
	{
		return [
			'success' => $this->result,
			'fields' => $this->errors,
			'message' => $this->message,
			'actions' => $this->actions,
			'data' => $this->data,
		];
	}

	/**
	 * Returns list of errors
	 *
	 * @return mixed[]
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}

	/**
	 * @inheritDoc
	 */
	public function toArray(): array
	{
		return $this->getResult();
	}

	/**
	 * Does result have errors
	 *
	 * @return boolean
	 */
	public function hasError(): bool
	{
		return !$this->result;
	}

	/**
	 * Set bulk errors
	 *
	 * @param mixed[] $errors
	 * @return void
	 */
	public function setErrors(array $errors)
	{
		$this->errors = $errors;
		$this->result = empty($errors);
	}
}
