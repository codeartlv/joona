<?php

namespace Codeart\Joona\Controllers\User;

use Codeart\Joona\Contracts\Form;
use Codeart\Joona\Models\User\AdminUser;
use Illuminate\Http\Request;
use Codeart\Joona\Facades\AdminAuth;
use Codeart\Joona\Helpers\FloodCheck;
use Codeart\Joona\Models\User\AdminSession;
use Codeart\Joona\Models\User\Log\Event\Login;
use Codeart\Joona\Models\User\Log\Event\Logout;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class AuthController
{
	public function authForm()
	{
		$continue = session('auth.url_intended') ?: route('joona.dashboard');
		$logout_message = session('auth.logout_message');

		return view('joona::user.auth-form', [
			'continue' => $continue,
			'logout_message' => $logout_message,
		]);
	}

	public function logoutProcess()
	{
		$user = AdminAuth::user();

		$user->logEvent(new Logout(), request()->ip());

		AdminSession::endSession($user, 'logout');
		AdminAuth::logout();

		return response()->redirectToRoute('joona.user.auth-form');
	}

	public function authProcess(Request $request)
	{
		$flood_interval = 10;
		$max_attempts = 4;

		$result = new Form();
		$flood = new FloodCheck('login');

		$credentials = $request -> only('username', 'password');

		$continue = $request -> get('continue');

		if (!$flood -> check($flood_interval, $max_attempts)) {
			$unlock_seconds = $flood->getUnlockTime() - time();
			$result -> setError(__('joona::common.throttle', ['seconds' => $unlock_seconds]));
			return response()->json($result);
		}

		$exists = AdminAuth::validate($credentials);

		if ($exists) {
			$user = AdminUser::where('username', $credentials['username'])->first();

			if (!$user->isActive()) {
				$result->setError(__('joona::user.account_inactive'));
				return response()->json($result);
			}
		}

		$authorized = AdminAuth::attempt($credentials);

		if (!$authorized) {
			$result -> setError(__('joona::user.auth.failed'));
			return response()->json($result);
		}

		$user = AdminAuth::user();

		AdminSession::startSession($user);

		$user->logEvent(new Login(), request()->ip());

		Session::forget('auth.url_intended');

		$result->setSuccess(__('joona::user.auth.success'));
		$result->setAction('redirect', $continue ?: route('joona.dashboard'));

		return response()->json($result);
	}
}
