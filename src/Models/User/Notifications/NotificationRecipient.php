<?php

namespace Codeart\Joona\Models\User\Notifications;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * @property Carbon|null $read_at
 * @package Codeart\Joona\Models\User\Notifications
 */
class NotificationRecipient extends Model
{
	public $timestamps = false;
	
	/**
	 * Models associated table
	 *
	 * @var string
	 */
	protected $table = 'admin_notifications_users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
		'notification_id',
		'user_id',
		'read_at',
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
			'read_at' => 'datetime',
		];
	}

}
