<?php

namespace Codeart\Joona\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  string[]  ...$guards
	 * @return mixed
	 */
	protected function authenticate($request, array $guards)
	{
		if (empty($guards)) {
			$guards = ['admin'];
		}

		foreach ($guards as $guard) {
			if ($this->auth->guard($guard)->check()) {
				return $this->auth->shouldUse($guard);
			}
		}

		$this->unauthenticated($request, $guards);
	}

	/**
	 * Get the path the user should be redirected to when they are not authenticated.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return string|null
	 */
	protected function redirectTo($request)
	{
		if (!$request->expectsJson()) {
			return route('joona.user.login');
		}
	}
}
