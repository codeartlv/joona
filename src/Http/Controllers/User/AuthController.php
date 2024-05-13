<?php

namespace Codeart\Joona\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Codeart\Joona\View\Components\Form\FormResponse;
use Codeart\Joona\Facades\Auth;

class AuthController
{
	/**
	 * Displays the user authorization form.
	 *
	 * @return \Illuminate\View\View The view instance for the login form.
	 */
	public function loginForm()
	{
		return view('joona::user.auth-form', [
			'logout_message' => session('auth.logout_message'),
		]);
	}

	/**
	 * Processes the user logout.
	 *
	 * @return \Illuminate\Http\RedirectResponse Redirects to the user login route.
	 */
	public function logoutProcess()
	{
		Auth::logout();

		return response()->redirectToRoute('joona.user.login');
	}

	/**
	 * Handles the user authorization process.
	 *
	 * This method includes rate limiting to prevent brute force attacks. If the
	 * rate limit is exceeded, it returns an error. Otherwise, it attempts to
	 * authorize the user with the provided credentials. On success, it redirects
	 * the user to the intended URL or the default dashboard. On failure, it
	 * returns an error.
	 *
	 * @param \Illuminate\Http\Request $request The incoming request instance.
	 * @return \Illuminate\Http\JsonResponse The response object containing the authorization result.
	 */
	public function authProcess(Request $request)
	{
		$form = new FormResponse();

		$throttleKey = 'joona-auth-form-' . $request->ip();
		$maxAttempts = 5;
		$decayMinutes = 0.5;

		if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
			$form -> setError(__('joona::common.throttle', [
				'seconds' => RateLimiter::availableIn($throttleKey)
			]));

			return response()->json($form);
		}

		RateLimiter::hit($throttleKey, $decayMinutes * 60);

		$credentials = $request -> only('email', 'password');
		$authorized = Auth::attempt($credentials);

		if (!$authorized) {
			$form -> setError(__('joona::user.auth.failed'));
			return response()->json($form);
		}

		$url = redirect()->getIntendedUrl();

		$form->setSuccess(__('joona::user.auth.success'));
		$form->setAction('redirect', $url ?? route('joona.dashboard'));

		return response()->json($form);
	}
}
