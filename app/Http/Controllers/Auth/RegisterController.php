<?php

namespace App\Http\Controllers\Auth;

use App\Tenant;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Arr;
use JWTAuth;
use App\Notifications\ActivateTenant;
use App\User;

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
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\User
	 */
	protected function create(StoreTenant $request)
	{
		$tenant = Tenant::create($request->only('name'));
		$request = $this->parse_user($request);
		$payload = $request->only(['firstname','lastname','email','password']);
		$payload["tenant_id"] = $tenant->id;
		$user = User::create($payload);

		$tenant->setOwner($user);
		$tenant->owner->notify(new ActivateTenant);

		$payStackCustomer = PayStack::createCustomer([
			'first_name' => $request->firstname,
			'last_name' => $request->lastname,
			'email' => $request->email
		]);

		$tenant->fill(['paystack_id' => $payStackCustomer->customer_code]);

		return response([
			'message' => 'Account created, check your email to activate your account',
		], 200);
	}

	private function parse_user(Request $request){
		$data = [];
		$fullName = explode(' ', $request->fullName);
		$data['firstname'] = $fullName[0];
		$data['lastname'] = isset($fullName[1]) ? $fullName[1] : '';
		$request->merge($data);

		return $request;
	}
}
