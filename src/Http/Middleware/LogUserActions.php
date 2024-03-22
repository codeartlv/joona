<?php

namespace Codeart\Joona\Http\Middleware;

use Closure;
use Codeart\Joona\Facades\Auth;
use Codeart\Joona\Models\User\AdminSession;
use PDO;

class LogUserActions
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$user = Auth::user();

		if ($user) {
			AdminSession::refreshSession($user);
		}

		return $next($request);
	}
}
