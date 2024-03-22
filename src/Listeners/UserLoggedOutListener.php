<?php

namespace Codeart\Joona\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Codeart\Joona\Models\User\Log\Event\Logout as LogoutEvent;
use Codeart\Joona\Models\User\AdminSession;

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

		$event->user->logEvent(new LogoutEvent(), request()->ip());

		AdminSession::endSession($event->user, 'logout');
	}
}
