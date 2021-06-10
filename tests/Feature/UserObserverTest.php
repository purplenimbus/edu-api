<?php

namespace Tests\Feature;

use App\User;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\SetupUser;
use Tests\TestCase;

class UserObserverTest extends TestCase
{
  use RefreshDatabase, SetupUser;

  /**
   * Test default account status
   *
   * @return void
   */
  public function testDefaultAccountStatus()
  {
    $this->seed(DatabaseSeeder::class);

    $user = factory(User::class)->make();

    $this->actingAs($this->user)
      ->postJson('api/v1/users', $user->only([
        'date_of_birth',
        'email',
        'firstname',
        'lastname',
      ]));

    $this->assertEquals(User::StatusTypes['created'], User::first()->account_status_id);
  }
}
