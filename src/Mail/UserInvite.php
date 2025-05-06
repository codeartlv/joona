<?php

namespace Codeart\Joona\Mail;

use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class UserInvite extends Mailable
{
	use Queueable, SerializesModels;

	/**
	 * Create a new message instance.
	 */
	public function __construct(public AdminUser $user, protected string $hash)
	{
		//
	}

	/**
	 * Get the message envelope.
	 */
	public function envelope(): Envelope
	{
		return new Envelope(
			subject: $this->getSubject(),
		);
	}

	/**
	 * Return subject of the message
	 *
	 * @return string
	 */
	private function getSubject(): string
	{
		$defaultLocale = (string) config('app.locale');
		return Lang::get('joona::user.mail_subject_invite', [], $defaultLocale);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(
			view: 'joona::mail.user_invite',
			with: $this->user->toArray() + [
				'subject' => $this->getSubject(),
				'url' => route('joona.user.invite', ['hash' => $this->hash]),
			]
		);
	}

	/**
	 * Get the attachments for the message.
	 *
	 * @return array<int, \Illuminate\Mail\Mailables\Attachment>
	 */
	public function attachments(): array
	{
		return [];
	}
}
