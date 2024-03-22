<?php

namespace Codeart\Joona\Enums;

use Codeart\Joona\Contracts\HasLabel;

/**
 * User level enumeration
 */
enum UserLevel: string implements HasLabel
{
	case Admin = 'admin';
	case User = 'user';

	public function getLabel(): string
	{
		return __('joona::user.level_names.'.$this->value);
	}
}
