<?php

namespace App\Http\Controllers\Auth;

use App\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Arr;
use JWTAuth;
use App\Notifications\ActivateTenant;

class RegisterController extends Controller
{
	/*
	|--------------------------------------------------------------------------
	| Register Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles the registration of new users as well as their
	| validation and creation. By default this controller uses a trait to
	| provide this functionality without requiring any additional code.
	|
	*/

	use RegistersUsers;

	/**
	 * Where to redirect users after registration.
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

	/**
	 * Get a validator for an incoming registration request.
	 *
	 * @param  array  $data
	 * @return \Illuminate\Contracts\Validation\Validator
	 */
	protected function validator(array $data)
	{
		return Validator::make($data, [
			'school_name' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:tenants',
			'password' => 'required|string|min:6|confirmed',
		]);
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\User
	 */
	protected function create(array $data)
	{
		$payload = Arr::only($data->all(), ['email','password','school_name']);
		$tenant = Tenant::create($payload);

		$tenant->notify(new ActivateTenant);

		return response([
			'message' => 'User account created , check your email to activate your account',
		], 200);
	}
	
	private function parse_full_name($data){
		$fullName = explode(' ', $data->fullName);
		$data['first_name'] = $fullName[0];
		$data['last_name'] = isset($fullName[1]) ? $fullName[1] : '';

	return $data;
	}
}
