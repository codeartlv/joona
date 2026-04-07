<?php

namespace Codeart\Joona\Models\User\Notifications;

use Illuminate\Support\Collection;

readonly class NotificationCollection
{
	/**
	 * 
	 * @param Collection<int,Notification> $notifications 
	 * @param bool $hasMore 
	 * @return void 
	 */
	public function __construct(
		public Collection $notifications,
		public bool $hasMore = false,
	) {

	}
}