<?php

namespace Codeart\Joona\Auth\Permissions;

/**
 * An abstract permission object
 *
 * @package Codeart\Joona\Auth\Permissions
 */
abstract class Permission
{
	protected ?string $id;
	protected ?string $label;
	protected bool $elevated = false;

	/**
	 * Returns permission keys
	 *
	 * @return array
	 */
	abstract protected function getKeys(): array;

	public function __construct(string $id, string $label, bool $elevated = false)
	{
		$this->id = $id;
		$this->label = $label;
		$this->elevated = $elevated;
	}

	/**
	 * Does permission require only super user
	 *
	 * @return bool
	 */
	public function isElevated(): bool
	{
		return $this->elevated;
	}

	/**
	 * Returns permission ID
	 *
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * Returns permission label
	 *
	 * @return string
	 */
	public function getLabel(): string
	{
		return $this->label;
	}

	/**
	 * Return permission keys
	 *
	 * @return array
	 */
	public function getAccessKeys(): array
	{
		return array_merge($this->getKeys(), [$this->getId()]);
	}
}
