<?php

namespace Tests\Feature;

use App\Guardian;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\SetupUser;
use Tests\TestCase;

class GuardianObserverTest extends TestCase
{
  use RefreshDatabase, SetupUser;
  /**
   * Test the default password
   *
   * @return void
   */
  public function testItSetsTheDefaultPassword()
  {
    $person = factory(User::class)->make();

    $this->actingAs($this->user)
      ->postJson("api/v1/guardians",
        $person->only([
          'email',
          'firstname',
          'lastname'
        ]));

    $this->assertNotEmpty(Guardian::first()->password);
  }

  /**
   * Test the default role
   *
   * @return void
   */
  public function testItSetsTheUsersRole()
  {
    $person = factory(User::class)->make();

    $this->actingAs($this->user)
      ->postJson("api/v1/guardians",
        $person->only([
          'email',
          'firstname',
          'lastname'
        ]));

    $this->assertEquals('guardian', Guardian::first()->type);
  }
}
