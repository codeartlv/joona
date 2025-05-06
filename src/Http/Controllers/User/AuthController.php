<?php

namespace Codeart\Joona\Http\Controllers\User;

use Codeart\Joona\Enums\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Codeart\Joona\View\Components\Form\FormResponse;
use Codeart\Joona\Facades\Auth;
use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

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
	 * Displays user invitation form.
	 *
	 * @return \Illuminate\View\View The view instance for the invite form.
	 */
	public function inviteForm(string $hash)
	{
		$user = AdminUser::where([
			'hash' => $hash,
			'status' => UserStatus::PENDING,
		])->first();

		if (!$user) {
			return view('joona::error', [
				'message' => __('joona::user.invite_link_invalid'),
			]);
		}

		return view('joona::user.invite-form', [
			'hash' => $hash,
			'user_email' => $user->email,
		]);
	}

	/**
	 * Handles the user registration process by invite.
	 *
	 * @param \Illuminate\Http\Request $request The incoming request instance.
	 * @return \Illuminate\Http\JsonResponse The response object containing the authorization result.
	 */
	public function inviteProcess(Request $request): JsonResponse
	{
		$form = new FormResponse();
		$hash = (string) $request->post('hash');

		$user = AdminUser::where([
			'hash' => $hash,
			'status' => UserStatus::PENDING,
		])->first();

		if (!$user) {
			$form->setError(__('joona::user.invite_link_invalid'));
			return response()->json($form);
		}

		$fields = [
			'first_name' => $request->post('first_name'),
			'last_name' => $request->post('last_name'),
			'password' => $request->post('password'),
		];

		AdminUser::createOrUpdate($fields, $user, $form);

		if (!$form->hasError()) {
			$form->setSuccess(__('joona::user.profile_saved'));
			$form->setAction('reset', true);
			$form->setAction('close_popup', true);
			$form->setAction('redirect', Joona::getBasePath());

			$user->update([
				'hash' => null,
				'status' => UserStatus::ACTIVE,
			]);

			Auth::login($user);
		}

		return response()->json($form);
	}

	/**
	 * Processes the user logout.
	 *
	 * @return \Illuminate\Http\RedirectResponse Redirects to the user login route.
	 */
	public function logoutProcess(): RedirectResponse
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
	public function authProcess(Request $request): JsonResponse
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

		$user = AdminUser::where('email', request()->post('email'))->first();
		$maxLoginAttempts = (int) config('joona.auto_block_user');

		if (!$user) {
			$form -> setError(__('joona::user.auth.failed'));
			return response()->json($form);
		}

		if (
			$user && //User exists
			$user->canAuthBeBlocked() && // Can be blocked
			$maxLoginAttempts >  0 && // Feature is enabled
			$user->failed_attempts >= $maxLoginAttempts && // Attempts exceeded
			$user->status == UserStatus::ACTIVE // Do not update already blocked
		) {
			$user->update([
				'status' => UserStatus::BLOCKED,
			]);
		}

		if ($user->status == UserStatus::BLOCKED) {
			$form -> setError(__('joona::user.auth.blocked'));
			return response()->json($form);
		}

		if ($user->status == UserStatus::PENDING) {
			$form -> setError(__('joona::user.auth.pending'));
			return response()->json($form);
		}

		$credentials = $request -> only('email', 'password');
		$authorized = Auth::attempt($credentials);

		if (!$authorized) {
			if ($user && $user->canAuthBeBlocked() && $maxLoginAttempts >  0) {
				$user->update([
					'failed_attempts' => $user->failed_attempts + 1,
				]);
			}

			$form -> setError(__('joona::user.auth.failed'));
			return response()->json($form);
		}

		$url = redirect()->getIntendedUrl();

		$form->setSuccess(__('joona::user.auth.success'));
		$form->setAction('redirect', $url ?? route('joona.dashboard'));

		$user->update([
			'failed_attempts' => 0,
		]);

		return response()->json($form);
	}
}
