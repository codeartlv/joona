<?php

namespace Codeart\Joona\Models\User\Log\Event;

use Codeart\Joona\Models\User\Log\LogEntry;
use Codeart\Joona\Models\User\Log\LogEvent;

class Login implements LogEvent
{
	public function getTitle(): string
	{
		return __('joona::user.log_entries.login');
	}

	public function getCategory(): string
	{
		return 'admin_user';
	}

	public function getDescription(LogEntry $entry): string
	{
		return (string) $entry->object_id;
	}
}
