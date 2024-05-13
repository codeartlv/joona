<?php

namespace Codeart\Joona\Mail;

use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

class UserPassword extends Mailable
{
	use Queueable, SerializesModels;

	/**
	 * Create a new message instance.
	 */
	public function __construct(public AdminUser $user, public string $password, public bool $isCreateNew)
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

		$keyword = $this->isCreateNew ? 'joona::user.mail_subject_new_user':'joona::user.mail_subject_new_password';

		return Lang::get($keyword, [], $defaultLocale);
	}

	/**
	 * Get the message content definition.
	 */
	public function content(): Content
	{
		return new Content(
			view: 'joona::mail.user_password',
			with: $this->user->toArray() + [
				'subject' => $this->getSubject(),
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
