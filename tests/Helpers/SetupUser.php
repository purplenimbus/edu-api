<?php

namespace Tests\Helpers;

use App\Tenant;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait SetupUser
{
  public $user;

  public function setUp(): void {
    parent::setUp();

    $tenant = factory(Tenant::class)->create();

    $this->user = factory(User::class)->create([
      'tenant_id' => $tenant->id
    ]);

    $this->user->markEmailAsVerified();

    auth()->login($this->user);
  }

    /**
   * Set the currently logged in user for the application.
   *
   * @param  \Illuminate\Contracts\Auth\Authenticatable $user
   * @param  string|null                                $driver
   * @return $this
   */
  public function actingAs($user, $driver = null)
  {
    $token = JWTAuth::fromUser($user);

    $this->withHeader('Authorization', "Bearer {$token}");

    parent::actingAs($user, "api");
    
    return $this;
  }
}
