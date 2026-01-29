<?php

namespace Codeart\Joona\Models\User\Notifications;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $type
 * @property string $notifiable_id
 * @property array $data
 * @property bool $is_global
 * 
 * @package Codeart\Joona\Models\User\Notifications
 */
class NotificationRecord extends Model
{
	/**
	 * Models associated table
	 *
	 * @var string
	 */
	protected $table = 'admin_notifications';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'type',
		'notifiable_id',
		'data',
		'is_global',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var array<int, string>
	 */
	protected $hidden = [
		
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array
	{
		return [
			'data' => 'json',
			'is_global' => 'bool',
		];
	}

	public function recipients()
	{
		return $this->hasMany(NotificationRecipient::class, 'notification_id');
	}

	public function getPresenter(): NotificationPresenter
	{
		$class = NotificationServer::getRegisteredClass($this->type);
		return new $class($this->notifiable_id, $this->data ?? []);
	}

	public function scopeForUser($query, $userId)
	{
		return $query->where(function ($q) use ($userId) {
			$q->where('is_global', true)
			->orWhereHas('recipients', function ($inner) use ($userId) {
				$inner->where('user_id', $userId);
			});
		});
	}
}
