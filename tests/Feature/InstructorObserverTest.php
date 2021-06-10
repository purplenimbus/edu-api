<?php

namespace Tests\Feature;

use App\Instructor;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Helpers\SetupUser;
use Tests\TestCase;
use Bouncer;

class InstructorObserverTest extends TestCase
{
  use RefreshDatabase, SetupUser;
  /**
   * Test the default password
   *
   * @return void
   */
  public function testSetsDefaultPassword()
  {
    $this->user->tenant->setOwner($this->user);

    $person = factory(User::class)->make();

    $this->actingAs($this->user)
      ->postJson("api/v1/instructors",
        $person->only([
          'email',
          'firstname',
          'lastname'
        ]));

    $this->assertNotEmpty(Instructor::first()->password);
  }

  /**
   * Test the default role
   *
   * @return void
   */
  public function testSetsTheUsersRole()
  {
    $this->user->tenant->setOwner($this->user);

    $person = factory(User::class)->make();

    $this->actingAs($this->user)
      ->postJson("api/v1/instructors",
        $person->only([
          'email',
          'firstname',
          'lastname'
        ]));

    $this->assertEquals('instructor', Instructor::first()->type);
  }
}
