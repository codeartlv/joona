<?php

namespace Codeart\Joona\Models\User\Notifications;

use Codeart\Joona\Facades\Auth;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Support\Collection;

class NotificationServer
{
	/**
	 * Registered notifications
	 * 
	 * @var array<int,NotificationPresenter>
	 */
	protected static $notifications = [];

	/**
	 * Register notification classes
	 * 
	 * @param array<int,class-string> $classes 
	 * @return void
	 */
	public static function register(array $classes): void
	{
		foreach ($classes as $class) {
			if (!class_exists($class)) {
				throw new InvalidArgumentException("Class {$class} does not exist.");
			}

			if (!is_subclass_of($class, NotificationPresenter::class)) {
				throw new InvalidArgumentException(
					"Class {$class} must implement " . NotificationPresenter::class
				);
			}

			self::$notifications[$class::getTypeIdentifier()] = $class;
		}
	}

    public static function getUnreadCount(?AdminUser $user = null, ?string $type = null): int
    {
		$user = $user ?? Auth::user();

		if (!$user) {
			return 0;
		}

        $query = NotificationRecord::forUser($user->id)
            ->whereDoesntHave('recipients', function ($q) use ($user) {
                $q->where('user_id', $user->id)->whereNotNull('read_at');
            });

        if ($type) {
            $query->where('type', $type);
        }

        return $query->count();
    }

	/**
     * Return a set of notifications
	 * 
     * @param AdminUser|null $user
     * @param bool $onlyUnread
     * @param int $limit
     * @param int|null $lastId
     * @return Collection<int,Notification>
     */
	public static function getNotifications(
		?AdminUser $user = null,
		bool $onlyUnread = false,
		int $limit = 15,
        ?int $lastId = null
	): Collection
    {
		$user = $user ?? Auth::user();

		if (!$user) {
			return collect([]);
		}

        $query = NotificationRecord::forUser($user->id);

        if ($onlyUnread) {
            $query->whereDoesntHave('recipients', function ($q) use ($user) {
                $q->where('user_id', $user->id)->whereNotNull('read_at');
            });
        }

        if ($lastId) {
            $query->where('id', '<', $lastId);
        }

        return $query->with(['recipients' => function($q) use ($user) {
            $q->where('user_id', $user->id);
        }])
        ->orderBy('id', 'desc')
        ->limit($limit)
        ->get()
		->map(function($record) use ($user) {
			$recipient = $record->recipients->first();
			$isRead = $recipient && $recipient->read_at !== null;
			
			return new Notification(
				id: (int) $record->id,
				user: $user,
				read: $isRead,
				createdAt: $record->created_at,
				isGlobal: $record->is_global,
				presenter: $record->getPresenter(),
			);
		});
    }

	public static function markAsRead(int $notifiableId, int $userId): void
	{
		NotificationRecipient::updateOrCreate(
			[
				'notification_id' => $notifiableId, 
				'user_id'         => $userId
			],
			[
				'read_at' => now()
			]
		);
	}

	public static function markAllAsRead(?AdminUser $user = null): void
	{
		$user = $user ?? Auth::user();

		if (!$user) {
			return;
		}

    	$notificationIds = NotificationRecord::forUser($user->id)
        ->whereDoesntHave('recipients', function ($q) use ($user) {
            $q->where('user_id', $user->id)->whereNotNull('read_at');
        })
        ->pluck('id');

    	foreach ($notificationIds as $id) {
        	self::markAsRead($id, $user);
    	}
	}

	public static function push(
		NotificationPresenter $message,
		bool $isGlobal = false,
		?AdminUser $user = null
	): void
	{
		$type = $message::getTypeIdentifier();
		
		if (!isset(self::$notifications[$type])) {
			return;
		}

		$record = NotificationRecord::create([
			'type' => $type,
			'notifiable_id' => $message->notifiableId,
			'data' => $message->data,
			'is_global' => $isGlobal,
		]);

		if (!$isGlobal && $user) {
			NotificationRecipient::create([
				'notification_id'=> $record->id,
				'user_id' => $user->id,
				'read_at' => null,
			]);
		}
	}

	public static function getRegisteredClass(string $className): ?string
	{
		return self::$notifications[$className] ?? null;
	}
}