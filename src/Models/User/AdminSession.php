<?php

namespace Codeart\Joona\Models\User;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * User session model. Holds information about when user logged in, and when logged out or actions ended.
 */
class AdminSession extends Model
{
	/**
	 * Model table
	 *
	 * @var string
	 */
	protected $table = 'admin_users_sessions';

	/**
	 * Do not use default timestamps
	 *
	 * @var boolean
	 */
	public $timestamps = false;

	/**
	 * Fields
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'started',
		'ended',
		'last_action',
		'login_ip',
		'end_reason',
	];

	/**
	 * Start new session. Should be called upon login.
	 *
	 * @return AdminSession
	 */
	public static function startSession(AdminUser $user)
	{
		self::endSession($user, 'auto');

		$now = date('Y-m-d H:i:s');

		$session = new AdminSession([
			'user_id' => $user->id,
			'started' => $now,
			'last_action' => $now,
			'login_ip' => request()->ip(),
		]);

		$session->save();

		$user->update([
			'logged_at' => $now,
			'logged_ip' => request()->ip(),
		]);

		return $session;
	}

	/**
	 * End active session
	 *
	 * @param string $reason End reason. Can be 'logout','stopped','auto'
	 * @return void
	 */
	public static function endSession(AdminUser $user, string $reason)
	{
		$check = AdminSession::where([
			'user_id' => $user->id,
			'ended' => null,
		])->get();

		if ($check->count()) {
			$check->each(function ($line) use ($reason) {
				$ended = $line->last_action ? $line->last_action : date('Y-m-d H:i:s');

				$line->update([
					'end_reason' => $reason,
					'ended' => $ended,
				]);
			});
		}
	}

	/**
	 * Refresh current active session, or create new, if it doesn't exist
	 *
	 * @return void
	 */
	public static function refreshSession(AdminUser $user)
	{
		$session = self::getActiveSession($user);

		if (!$session) {
			$session = self::startSession($user);
		}

		$session->update([
			'last_action' => date('Y-m-d H:i:s'),
		]);

		return true;
	}

	/**
	 * Get current active session
	 *
	 * @return AdminSession|null
	 */
	public static function getActiveSession(AdminUser $user): ?AdminSession
	{
		$session_id = AdminSession::where([
			'user_id' => $user->id,
			'ended' => null,
		])->get();

		if (!$session_id) {
			return null;
		}

		return $session_id->first();
	}

	/**
	 * Get session user
	 *
	 * @return HasOne
	 */
	public function user(): HasOne
	{
		return $this->hasOne(AdminUser::class, 'id', 'user_id')->withTrashed();
	}
}
