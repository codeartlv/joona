<?php

namespace Codeart\Joona\Models\User\Log;

use Illuminate\Database\Eloquent\Model;

/**
 * User journal entry.
 */
class LogEntry extends Model
{
	/**
	 * Model table
	 *
	 * @var string
	 */
	protected $table = 'admin_users_log';

	/**
	 * Fields
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'session_id',
		'action',
		'object_id',
		'ua',
	];

	public static function getCategoryFromEvent(LogEvent $event): string
	{
		return strtolower(class_basename($event));
	}

	public static function getEventFromCategory(string $category): ?LogEvent
	{
		$class = '\\'.__NAMESPACE__.'\\Event\\'.ucfirst($category);
		return new $class;
	}
}
