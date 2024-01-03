<?php

namespace Codeart\Joona\Models\User\Log\Event;

use Codeart\Joona\Models\User\Log\LogEntry;
use Codeart\Joona\Models\User\Log\LogEvent;

class Logout implements LogEvent
{
	public function getTitle(): string
	{
		return __('joona::user.log_entries.logout');
	}

	public function getDescription(LogEntry $entry): string
	{
		return (string) $entry->object_id;
	}
}
