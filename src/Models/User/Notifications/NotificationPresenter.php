<?php

namespace Codeart\Joona\Models\User\Notifications;

abstract readonly class NotificationPresenter
{
	abstract public static function getTypeIdentifier(): string;
	abstract public function getTitle(): string;
    
	public function getMessage(): ?string {
		return null;
	}
	
    public function getUrl(): ?string
	{
		return null;
	}

    public function getIcon(): ?string
	{
		return null;
	}

	public function autoMarkAsRead(): bool
	{
		return true;
	}
	
	public function __construct(
		public string $notifiableId,
		public array $data = []
	)
	{
		;
	}
}