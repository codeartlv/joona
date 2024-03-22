<?php

namespace Codeart\Joona\Listeners;

use Illuminate\Auth\Events\Login;
use Codeart\Joona\Models\User\Log\Event\Login as LoginEvent;
use Codeart\Joona\Models\User\AdminSession;

class UserLoggedInListener
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
	public function handle(Login $event): void
	{
		$user = $event->user;

		AdminSession::startSession($user);
		$user->logEvent(new LoginEvent(), request()->ip());
	}
}
