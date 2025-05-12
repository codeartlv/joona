<?php

namespace Codeart\Joona\Http\Controllers\User;

use Codeart\Joona\Enums\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Codeart\Joona\View\Components\Form\FormResponse;
use Codeart\Joona\Facades\Auth;
use Codeart\Joona\Facades\Joona;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
	 * Displays password recover form
	 *
	 * @return \Illuminate\View\View The view instance for the invite form.
	 */
	public function recoverForm(): View
	{
		return view('joona::user.recover-form', [
			'sent' => session('recover_sent'),
		]);
	}

	/**
	 * Displays new password setup form
	 *
	 * @param \Illuminate\Http\Request $request The incoming request instance.
	 * @return \Illuminate\View\View The view instance for the invite form.
	 */
	public function recoverSetForm(Request $request): View
	{
		$token = (string) $request->get('token');
		$email = (string) $request->get('email');

		return view('joona::user.recover-password', [
			'token' => $token,			
			'provided_email' => $email,			
		]);
	}

	/**
	 * Displays password recover form
	 *
	 * @param \Illuminate\Http\Request $request The incoming request instance.
	 * @return JsonResponse The response object containing the form process result.
	 */
	public function recoverStart(Request $request): JsonResponse
	{
		$form = new FormResponse();
		$email = strtolower((string) $request->post('email'));

		if (!$email) {
			$form->setError(__('joona::common.email_required'), 'email');
			return response()->json($form);
		}

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$form->setError(__('joona::common.email_invalid'), 'email');
			return response()->json($form);
		}

		$throttleKey = 'joona-recover-form-' . $request->ip();
		$maxAttempts = 5;
		$decayMinutes = 0.5;

		if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
			$form -> setError(__('joona::common.throttle', [
				'seconds' => RateLimiter::availableIn($throttleKey)
			]));

			return response()->json($form);
		}

		RateLimiter::hit($throttleKey, $decayMinutes * 60);

		$user = AdminUser::where([
			'email' => $email,
			'status' => UserStatus::ACTIVE,
		])->first();

		$showSuccess = function() use ($form) {
			$form->setSuccess(__('joona::common.data_saved'));
			$form->setAction('reset', true);
			$form->setAction('reload', true);

			Session::flash('recover_sent', 1);

			return response()->json($form);
		};

		if (!$user) {
			return $showSuccess();
		}

		Password::broker('admins')->sendResetLink(
			$request->only('email')
		);

		return $showSuccess();
	}

	/**
	 * Displays password setup form
	 *
	 * @param \Illuminate\Http\Request $request The incoming request instance.
	 * @return JsonResponse The response object containing the form process result.
	 */
	public function recoverFinish(Request $request): JsonResponse
	{
		$form = new FormResponse();
		$password = (string) $request->post('password');
		$token = (string) $request->post('token');
		$email = (string) $request->post('email');

		if (!$password) {
			$form->setError(__('joona::user.recover.password_required'), 'password');
			return response()->json($form);
		}

		if (!AdminUser::isSecurePassword($password)) {
			$form->setError(__('joona::validation.password.unsecure'), 'password');
			return response()->json($form);
		}

		$status = Password::broker('admins')->reset(
            [
				'password' => $password,
				'token' => $token,
				'email' => $email,
			],
            function ($user, string $password) {
				$user->setPassword($password, true);
				Auth::login($user);
            }
        );

		if ($status === Password::PASSWORD_RESET) {
			$form->setSuccess(__('joona::user.recover.success'));
			$form->setAction('redirect', Joona::getBasePath());
		} else {
			$form->setError(__('joona::user.recover.failed'));
		}

		return response()->json($form);
	}

	/**
	 * Handles the user registration process by invite.
	 *
	 * @param \Illuminate\Http\Request $request The incoming request instance.
	 * @return JsonResponse The response object containing the form process result.
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
