<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTFactory;
use App\Tenant as Tenant;
use App\User as User;
use Pusher\Pusher as Pusher;

use App\Http\Requests\PusherAuth as PusherAuth;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  var $pusher;
  /**
   * Where to redirect users after login.
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
    $this->pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));
  }
	
	public function authenticate(Request $request)
  {
    $credentials = $request->only('email', 'password');
		
		$expDate = $request->has('remember_me') && $request->remember_me ? 14 : 1;
    $claims = ['exp' => Carbon::now()->addDays($expDate)->timestamp];

		try {
			if (!$token = JWTAuth::claims($claims)->attempt($credentials)) {
				return response()->json(['error' => 'invalid_credentials'], 401);
			}
		} catch (JWTException $e) {
			return response()->json(['error' => 'could_not_create_token'], 500);
		}

		return response()->json(compact(['token']));
  }
	
	public function getTenant($tenant){
		try{
			$tenant = Tenant::where('username', $tenant)->first();

			return $tenant;
		}catch(Exception $e){
			return false;
		}
	}

  public function pusher(PusherAuth $auth){
    return $this->pusher->socket_auth($auth->channel_name,$auth->socket_id); 
  }

  public function logout(){
    if(Auth::user()){
        Auth::user()->logout();
    }
  }
}
