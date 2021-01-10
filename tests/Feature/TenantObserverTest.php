<?php

namespace Tests\Feature;

use App\PaymentProfileItemType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\Helpers\Auth\SetupUser;
use Tests\TestCase;

class TenantObserverTest extends TestCase
{
  use RefreshDatabase, WithFaker;
  /**
   * Creates a default payment profile items
   *
   * @return void
   */
  public function testCreateDefaultPaymentProfileItemTypes()
  {
    $response = $this->postJson('/api/v1/register', [
      'email' => $this->faker->email,
      'fullName' => $this->faker->name,
      'name' => $this->faker->company,
      'password' => '1234abcd',
      'password_confirmation' => '1234abcd',
    ]);

    $response->assertStatus(200);
    $this->assertEquals(Arr::pluck(config('edu.default.payment_item_types'), 'name'), PaymentProfileItemType::all()->pluck('name')->toArray());
  }
}
