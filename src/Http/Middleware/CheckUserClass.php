<?php

namespace Codeart\Joona\Http\Middleware;

use Closure;
use Codeart\Joona\Facades\Auth;
use Codeart\Joona\Facades\Joona;

class CheckUserClass
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next, ...$checkClasses)
	{
		$classes = Joona::getUserClasses();

		if (empty($classes)) {
			return $next($request);
		}

		if (Auth::check()) {
			$user = Auth::user();

			if (!$user->class) {
				abort(403);
			}

			foreach ($checkClasses as $class) {
				$className = $class instanceof \UnitEnum ? $class->value : $class;

				if ($user->class === $className) {
					return $next($request);
				}
			}
		}

		abort(403);
	}
}

