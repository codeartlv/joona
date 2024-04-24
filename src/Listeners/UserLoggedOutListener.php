<?php

namespace Codeart\Joona\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Codeart\Joona\Models\User\Log\Event\Logout as LogoutEvent;
use Codeart\Joona\Models\User\AdminSession;
use Codeart\Joona\Models\User\AdminUser;

class UserLoggedOutListener
{
	/**
	 * Create the event listener.
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 */
	public function handle(Logout $event): void
	{
		if (!$event->user) {
			return;
		}

		if ($event->user instanceof AdminUser) {
			$event->user->logEvent(new LogoutEvent(), request()->ip());

			AdminSession::endSession($event->user, 'logout');
		}
	}
}
