<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Http\Requests\ResetUserPassword;
use App\Http\Requests\GetTokenResetUserPassword;
use App\Notifications\PasswordResetSuccess;
use Carbon\Carbon;
use JWTAuth;
use App\PasswordReset;
use App\User;

class ResetPasswordController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;

	/**
	 * Where to redirect users after resetting their password.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

		
	public function getToken(GetTokenResetUserPassword $token){
		$passwordReset = PasswordReset::where('token', $token)
			->first();

		if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
				$passwordReset->delete();
			return response()->json([
				'message' => 'Invalid token',
				'errors' => [
					'token' => ['This password reset token is invalid.'],
				],
			], 404);
		}

		return response()->json($passwordReset);
	}

	public function reset(ResetUserPassword $request){

		$passwordReset = PasswordReset::where('token', $request->token)->first();

		$user = User::where('email', $passwordReset->email)->first();

		$user->password = $request->password;

		$user->save();

		$passwordReset->delete();

		$user->notify(new PasswordResetSuccess());

		return response()->json($user);
	}
}
