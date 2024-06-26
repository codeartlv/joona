<?php

namespace Codeart\Joona\Models\User\Log;

interface LogEvent
{
	public function getTitle(): string;
	public function getCategory(): string;
	public function getDescription(LogEntry $entry): string;
}
