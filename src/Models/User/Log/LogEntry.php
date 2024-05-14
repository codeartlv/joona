<?php

namespace Codeart\Joona\Models\User\Log;

use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
		'category',
		'object_id',
		'parameters',
		'ua',
	];

	public static function getCategoryFromEvent(LogEvent $event): string
	{
		return get_class($event);
	}

	public static function getEventFromCategory(string $class): ?LogEvent
	{
		return new $class;
	}

	public function user(): HasOne
	{
		return $this->hasOne(AdminUser::class, 'id', 'user_id');
	}
}
