<?php

namespace Codeart\Joona\Contracts;

/**
 * Binds object to have provide a label
 *
 * @package Codeart\Joona\Contracts
 */
interface HasLabel
{
	/**
	 * Return object label
	 *
	 * @return string
	 */
	public function getLabel(): string;
}
