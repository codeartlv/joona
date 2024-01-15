<?php

namespace Codeart\Joona\Middleware;

use Closure;
use Codeart\Joona\Facades\AdminAuth;
use Codeart\Joona\Models\User\AdminSession;
use PDO;

class LogActions
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
		$user = AdminAuth::user();

		if ($user) {
			AdminSession::refreshSession($user);
		}

		return $next($request);
	}
}
