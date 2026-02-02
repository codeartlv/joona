<?php

namespace Codeart\Joona\Models\User\Notifications;

use Carbon\Carbon;
use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Models\User\AdminUser;

/**
 * Represents compund data for notification
 *  
 * @package Codeart\Joona\Models\User\Notifications
 */
readonly class Notification
{
	public function __construct(
		public int $id,
		public bool $read,
		public bool $isGlobal,
		public Carbon $createdAt,
		public NotificationPresenter $presenter,
		public AdminUser $user
	)
	{
		;
	}

	/**
	 * Mark notification as read
	 * 
	 * @return void 
	 */
	public function markAsRead(): void
	{
		NotificationServer::markAsReadByNotificationId($this->id, $this->user->id);
	}

	/**
	 * Return default image for notification, if notification did not supply a
	 * custom image.
	 * 
	 * @return string 
	 */
	public function getImage(): string
	{
		$image = $this->presenter->getIcon();

		if (!$image) {
			$image = Joona::getLogo('light');
		}

		return $image;
	}
}