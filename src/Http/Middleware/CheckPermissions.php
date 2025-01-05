<?php

namespace Codeart\Joona\Http\Middleware;

use Closure;
use Codeart\Joona\Facades\Auth;
use Codeart\Joona\Facades\Permission;
use Illuminate\Support\Facades\Gate;

class CheckPermissions
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
		if (Auth::check()) {
			$route = $request->route();

			if (is_object($route) && method_exists($route, 'getName')) {
				$active_route = $route->getName();

				if (Permission::isDefined($active_route) && Gate::denies($active_route)) {
					abort(403);
				}
			}
		}

		return $next($request);
	}
}
