<?php

namespace Tests\Unit;

use App\PaymentProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\Auth\SetupUser;

class PaymentProfileControllerTest extends TestCase
{
  use RefreshDatabase, SetupUser;
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
}
