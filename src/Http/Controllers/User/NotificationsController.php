<?php

namespace Codeart\Joona\Http\Controllers\User;

use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Models\User\Notifications\NotificationServer;
use Illuminate\Http\JsonResponse;

class NotificationsController
{
	/**
	 * Returns list of notifications for current user
	 *
	 * @return JsonResponse The view with the user's profile data.
	 */
	public function list(): JsonResponse
	{
		$limit = 15;

		$list = NotificationServer::getNotifications(
			limit: $limit,
			lastId: (int) request()->query('page')
		);

		$html = view('joona::common.notifications', [
			'notifications' => $list,
		])->render();

		foreach ($list as $item) {
			if ($item->presenter->autoMarkAsRead()) {
				$item->markAsRead();
			}
		}

		$count = NotificationServer::getUnreadCount();

		return response()->json([
			'badge' => $count,
			'complete' => $list->count() < $limit,
			'content' => $html,
		]);
	}

	public function count(): JsonResponse
	{
		$count = NotificationServer::getUnreadCount();

		return response()->json([
			'badge' => $count,
		]);
	}
}
