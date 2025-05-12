<?php

namespace Codeart\Joona\Notifications;

use Codeart\Joona\Mail\ResetPassword;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;

class AdminResetPassword extends Notification
{
    public function __construct(
		protected AdminUser $user,
        protected string $token
    ) {}

    /**
     * Channels this notification should use.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the reset-password e-mail.
     */
    public function toMail($notifiable): Mailable
    {
        $url = url(route('joona.user.recover-set', [
			'token' => $this->token,
			'email' => $this->user->email,
		], false));

		return (new ResetPassword($this->user, $url))->to($this->user->email);
    }
}
