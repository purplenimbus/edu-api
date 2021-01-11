<?php

namespace Tests\Unit;

use App\PaymentProfile;
use App\PaymentProfileItemType;
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
  public function testUpdateTenantPaymentProfile()
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

  /**
   * Update a tenant payment profile
   *
   * @return void
   */
  public function testUpdateTenantPaymentProfileItems()
  {
    $payment_profile = PaymentProfile::create([
      'name' => 'old default',
      'tenant_id' => $this->user->tenant->id
    ]);

    $adminPaymentProfileType = PaymentProfileItemType::whereName('administrative')
      ->ofTenant($this->user->tenant->id)
      ->first();

    $tuitionPaymentProfileType = PaymentProfileItemType::whereName('tuition')
      ->ofTenant($this->user->tenant->id)
      ->first();

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/payment_profiles/{$payment_profile->id}", [
        'items' => [
          [
            'amount' => 100,
            'description' => 'test',
            'type_id' => $adminPaymentProfileType->id,
          ],
          [
            'amount' => 200,
            'description' => 'test 2',
            'type_id' => $adminPaymentProfileType->id,
          ],
          [
            'amount' => 150,
            'description' => 'test 3',
            'type_id' => $tuitionPaymentProfileType->id,
          ],
        ],
      ]);
    
    $response->assertStatus(200);
    $this->assertEquals(3, PaymentProfile::first()->items->count());
    $this->assertEquals(450, PaymentProfile::first()->total);
  }
}
