<?php

namespace Codeart\Joona\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Route;
use Codeart\Joona\Facades\AdminAuth;
use Codeart\Joona\Facades\Permission;
use Codeart\Joona\Models\User\Access\PermissionLoader;
use Codeart\Joona\Models\User\AdminUser;
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
		if (AdminAuth::check()) {
			$user = AdminAuth::user();
			$route = $request->route();

			if (is_object($route) && method_exists($route, 'getName')) {
				$active_route = $route->getName();

				if (Permission::isDefined($active_route) && Gate::denies($active_route)) {
					abort(403);
				}
			}

			if (!$user->isActive()) {
				AdminAuth::logout();

				return
					redirect(route('joona.user.auth-form'))->
					with('auth.logout_message', __('joona::user.account_inactive'));
			}
		}

		return $next($request);
	}
}
