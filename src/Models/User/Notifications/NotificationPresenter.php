<?php

namespace Codeart\Joona\Models\User\Notifications;

abstract readonly class NotificationPresenter
{
	/**
	 * Return notification identifier
	 * @return string 
	 */
	abstract public static function getTypeIdentifier(): string;

	/**
	 * Return group name for related notifications
	 * 
	 * @return string 
	 */
	abstract public static function getGroup(): string;

	/**
	 * Return group identifier for related notifications
	 * 
	 * @return null|string 
	 */
	abstract public function getGroupIdentifier(): ?string;
	
	/**
	 * Display notification caption
	 * 
	 * @return string 
	 */
	abstract public function getTitle(): string;
    
	/**
	 * Notification message
	 * 
	 * @return null|string 
	 */
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

	/**
	 * Return URL where user should be reidrected
	 * 
	 * @return null|string 
	 */
    public function getUrl(): ?string
	{
		return null;
	}

	/**
	 * Return URL to notification image/icon
	 * 
	 * @return null|string 
	 */
    public function getIcon(): ?string
	{
		return null;
	}

	/**
	 * Auto mark notification as read when displaying in feed
	 * 
	 * @return bool 
	 */
	public function autoMarkAsRead(): bool
	{
		return true;
	}
	
	/**
	 * @param string|int $notifiableId 
	 * @param string|null $groupId 
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

	/**
	 * Mark notification as read
	 * 
	 * @return void 
	 */
	public function markAsRead(): void
	{
		NotificationServer::markAsRead(
			type: $this->getTypeIdentifier(),
			notifiableId: $this->notifiableId,
		);
	}

}