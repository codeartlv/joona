<?php

namespace Codeart\Joona\Models\User\Notifications;

use Carbon\Carbon;
use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Models\User\AdminUser;

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

	public function markAsRead(): void
	{
		NotificationServer::markAsRead($this->id, $this->user->id);
	}

	public function getImage(): string
	{
		$image = $this->presenter->getIcon();

		if (!$image) {
			$image = Joona::getLogo('light');
		}

		return $image;
	}
}