<?php

namespace Tests\Feature;

use App\Instructor;
use App\Notifications\ActivateUser;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\SetupUser;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;

class InstructorObserverTest extends TestCase
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
  public function testItSetsTheUsersRole()
  {
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

  public function testItSendsTheUserActivationEmail()
  {
    Notification::fake();
    $instructor = factory(Instructor::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);

    Notification::assertSentTo(
      [$instructor], ActivateUser::class
    );
  }
}
