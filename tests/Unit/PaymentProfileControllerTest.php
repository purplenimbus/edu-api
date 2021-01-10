<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\TestCase;
use Tests\Helpers\Auth\SetupUser;

class PaymentProfileControllerTest extends TestCase
{
  use RefreshDatabase, SetupUser;
  /**
   * A basic unit test example.
   *
   * @return void
   */
  public function testGetTenantPaymentProfiles()
  {
    $response = $this->actingAs($this->user)
      ->getJson('api/v1/payment_profiles');
    
    $response->assertStatus(200);
  }
}
