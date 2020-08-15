<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Notifications\PasswordResetRequest;
use App\PasswordReset;
use App\User;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
	/*
		|--------------------------------------------------------------------------
		| Password Reset Controller
		|--------------------------------------------------------------------------
		|
		| This controller is responsible for handling password reset emails and
		| includes a trait which assists in sending these notifications from
		| your application to your users. Feel free to explore this trait.
		|
		*/

	use SendsPasswordResetEmails;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Create token password reset email
	 *
	 * @param  [string] email
	 * @return [string] message
	 */
	public function sendResetLinkEmail(Request $request)
	{
		$request->validate([
			'email' => 'required|exists:users,email|string|email',
		]);

		$user = User::where('email', $request->email)->first();

		$passwordReset = PasswordReset::updateOrCreate(
			['email' => $user->email],
			[
				'token' => str_random(60),
			]
		);

		$user->notify(new PasswordResetRequest($passwordReset->token));

		return response()->json([
			'message' => 'We have e-mailed your password reset link!'
		]);
	}
}
