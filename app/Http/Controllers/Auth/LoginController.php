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
        //parent::__construct();
        //$this->middleware('guest')->except('logout');
        $this->pusher = new Pusher(env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'));

    }
	
	public function authenticate(Request $request)
    {
		
        $credentials = $request->only('email', 'password');

		try {
			// verify the credentials and create a token for the user
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json(['error' => 'invalid_credentials'], 401);
			}
		} catch (JWTException $e) {
			// something went wrong
			return response()->json(['error' => 'could_not_create_token'], 500);
		}
		
		$user = Auth::user()->load(['tenant:id,name','user_type:name,id','account_status:name,id','access_level:name,id']);

		return response()->json(compact(['token','user']));

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
        //if(Auth::check())
        //{
            return $this->pusher->socket_auth($auth->channel_name,$auth->socket_id); 
        //}else{
            //return response()->json(['message'=>'Forbidden'],403);
        //}
    }
}
