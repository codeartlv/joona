<?php

namespace Codeart\Joona\Models\User\Notifications;

abstract readonly class NotificationPresenter
{
	abstract public static function getTypeIdentifier(): string;
	abstract public function getTitle(): string;
    
	public function getMessage(): ?string {
		return null;
	}
	
	/**
	 * @return array<string,string>
	 */
    public function getUrlAttributes(): array
	{
		return [];
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
	
	/**
	 * @param string|int $notifiableId 
	 * @param array<int|string,int|bool|string> $data 
	 * @return void 
	 */
	public function __construct(
		public string|int $notifiableId,
		public array $data = []
	)
	{
		;
	}
}