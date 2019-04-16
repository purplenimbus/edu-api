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
  
  public function authenticate(Request $request)
  {
    $credentials = $request->only('email', 'password');
    try {
      if (! $token = JWTAuth::attempt($credentials)) {
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
}
