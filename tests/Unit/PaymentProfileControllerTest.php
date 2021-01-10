<?php

namespace Tests\Unit;

use App\PaymentProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\Auth\SetupUser;

class PaymentProfileControllerTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;
  /**
   * Get a tenants payment profiles
   *
   * @return void
   */
  public function testGetTenantPaymentProfiles()
  {
    $response = $this->actingAs($this->user)
      ->getJson('api/v1/payment_profiles');

    $response->assertStatus(200);
  }

  /**
   * Create a tenant payment profile
   *
   * @return void
   */
  public function testCreateTenantPaymentProfiles()
  {
    $response = $this->actingAs($this->user)
      ->postJson('api/v1/payment_profiles', [
        'name' => 'default',
      ]);

    $response->assertStatus(200);
    $this->assertEquals('default', PaymentProfile::first()->name);
  }

  /**
   * Update a tenant payment profile
   *
   * @return void
   */
  public function testUpdateTenantPaymentProfiles()
  {
    $payment_profile = PaymentProfile::create([
      'name' => 'old default',
      'tenant_id' => $this->user->tenant->id
    ]);
  
    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$payment_profile->id}", [
        'name' => 'new default',
      ]);

    $response->assertStatus(200);
    $this->assertEquals('new default', PaymentProfile::first()->name);
  }
}
