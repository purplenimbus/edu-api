<?php

namespace App\Http\Controllers\Auth;

use App\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;
use App\Services\Billing\Subscription;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Email Verification Controller
  |--------------------------------------------------------------------------
  |
  | This controller is responsible for handling email verification for any
  | tenant$tenant that recently registered with the application. Emails may also
  | be re-sent if the tenant$tenant didn't receive the original email message.
  |
  */

  use VerifiesEmails;

  /**
   * Where to redirect Tenants after verification.
   *
   * @var string
   */
  protected $redirectTo = '';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('signed')->only('verify');
    $this->middleware('throttle:6,1')->only('verify', 'resend');
  }

  /**
    * Verify Tenant Account.
    *
    * @param  array  $data
    * @return \App\Tenant
    */
  protected function verify(Request $request)
  {
    $tenant = Tenant::find($request->route('id'));
    $message = '';

    if (is_null($tenant)) {
      $message = 'Tenant dosent exist';
      return $this->get_view($message);
    }

    if ($request->route('id') != $tenant->getKey()) {
      $e = new AuthorizationException;
      return $this->get_view($e->GetMessage());
    }

    if ($tenant->hasVerifiedEmail()) {
      $message = 'Tenant already verified';
      return $this->get_view($message, true);
    }

    if ($tenant->markEmailAsVerified()) {
      event(new Verified($tenant));
      //begin trial here paystack? flutterwave? stripe?
    }

    $message = 'Tenant successfully verified';

    return $this->get_view($message, true);
  }

  private function get_view($message, $show_login = false) {
    $url = env('FRONT_END_URL');
    return view('auth.verify',   [
      'message' => $message,
      'login_url' => "${url}auth/login",
      'show_login' => $show_login,
    ]);
  }
}
