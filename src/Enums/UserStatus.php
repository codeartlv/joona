<?php

namespace Codeart\Joona\Enums;

use Codeart\Joona\Contracts\HasLabel;

/**
 * User status enumeration
 */
enum UserStatus: string implements HasLabel
{
	case ACTIVE = 'active';
	case BLOCKED = 'blocked';

	public function getLabel(): string
	{
		return __('joona::user.status_names.'.$this->value);
	}

	public function getClass(): string
	{
		return match ($this) {
			self::ACTIVE => 'success',
			self::BLOCKED => 'danger',
		};
	}
}
