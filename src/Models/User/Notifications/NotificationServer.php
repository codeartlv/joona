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
	
	/**
	 * Return number of unread notifications
	 * 
	 * @param null|AdminUser $user 
	 * @param null|string $type 
	 * @param null|string $group 
	 * @param null|string $groupId 
	 * @param null|int $notifiableId 
	 * @return int 
	 */
    public static function getUnreadCount(
		?AdminUser $user = null,
		?string $type = null,
		?string $group = null,
		?string $groupId = null,
		?int $notifiableId = null,
	): int {
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

        if ($group) {
            $query->where('group', $group);
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

		if ($notifiableId) {
			$query->where('notifiable_id', $notifiableId);
		}

        return $query->count();
    }

	/**
     * Return a set of notifications
	 * 
     * @param AdminUser|null $user
     * @param string|null $group
     * @param string|null $groupId
     * @param bool $onlyUnread
     * @param int $limit
     * @param int|null $lastId
     * @return Collection<int,Notification>
     */
	public static function getNotifications(
		?AdminUser $user = null,
		?string $group = null,
		?string $groupId = null,
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

		if ($group) {
            $query->where('group', $group);
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
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

	/**
	 * Mark specific notifications as read
	 * 
	 * @param null|AdminUser $user 
	 * @param null|int $notifiableId 
	 * @param null|string $type 
	 * @param null|string $group 
	 * @param null|string $groupId 
	 * @return void 
	 */
	public static function markAsRead(
		?AdminUser $user = null,
		?int $notifiableId = null,
		?string $type = null,
		?string $group = null,
		?string $groupId = null,
	): void
	{
		$user = $user ?? Auth::user();

		if (!$user) {
			return;
		}

		$query = NotificationRecord::forUser($user->id)
        ->whereDoesntHave('recipients', function ($q) use ($user) {
            $q->where('user_id', $user->id)->whereNotNull('read_at');
        });

		if ($notifiableId) {
			$query->where('notifiable_id', $notifiableId);
		}

		if ($type) {
			$query->where('type', $type);
		}

		if ($group) {
			$query->where('group', $group);
		}

		if ($groupId) {
			$query->where('group_id', $groupId);
		}

		$query->get()->each(function($notification) use ($user) {
			self::markAsReadByNotificationId($notification->id, $user->id);
		});
	}

	/**
	 * Mark notification read by it's ID
	 * 
	 * @param int $notificationId 
	 * @param int $userId 
	 * @return void 
	 */
	public static function markAsReadByNotificationId(int $notificationId, int $userId): void
	{
		NotificationRecipient::updateOrCreate(
			[
				'notification_id' => $notificationId, 
				'user_id' => $userId
			],
			[
				'read_at' => now()
			]
		);
	}

	/**
	 * Mark all notifications as read
	 * 
	 * @param null|AdminUser $user 
	 * @return void 
	 */
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
        	self::markAsReadByNotificationId($id, $user);
    	}
	}

	/**
	 * Push new notification
	 * 
	 * @param NotificationPresenter $message 
	 * @param bool $isGlobal 
	 * @param null|AdminUser $user 
	 * @return void 
	 */
	public static function push(
		NotificationPresenter $message,
		bool $isGlobal = false,
		?AdminUser $user = null
	): void
	{
		$type = $message::getTypeIdentifier();
		$group = $message::getGroup();
		
		if (!isset(self::$notifications[$type])) {
			return;
		}

		if (!$isGlobal && !$user) {
			return;
		}

		$record = NotificationRecord::create([
			'type' => $type,
			'notifiable_id' => $message->notifiableId,
			'group' => $group,
			'group_id' => $message->getGroupIdentifier(),
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

	/**
	 * Return notification type class
	 * 
	 * @param string $className 
	 * @return null|string 
	 */
	public static function getRegisteredClass(string $className): ?string
	{
		return self::$notifications[$className] ?? null;
	}
}