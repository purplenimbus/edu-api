<?php

namespace Tests\Unit;

use App\Jobs\ProcessBatch;
use App\StudentGrade;
use App\Tenant;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\SetupUser;
use Illuminate\Support\Facades\Bus;

class UserControllerTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;
  /**
   * Get all users for a tenant
   *
   * @return void
   */
  public function testItReturnsUsers()
  {
    $this->user->update(["firstname" => "anthony"]);
    $user1 = factory(User::class)->create([
      "firstname" => "james",
      "tenant_id" => $this->user->tenant_id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    $user2 = factory(User::class)->create([
      "firstname" => "benjamin",
      "tenant_id" => $tenant2->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/users")
      ->assertOk()
      ->assertJson([
        "data" => [
          $this->user->only(["id", "firstname"]),
          $user2->only(["id", "firstname"]),
          $user1->only(["id", "firstname"]),
        ],
      ]);
  }

  /**
   * Get all users filtered by first name for a tenant
   *
   * @return void
   */
  public function testItReturnsUsersFilteredByFirstName()
  {
    $user1 = factory(User::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      "tenant_id" => $tenant2->id,
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[firstname]={$user1->firstname}")
      ->assertOk()
      ->assertJson([
        "data" => [
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
          ],
        ],
      ]);
  }

  /**
   * Get all users filtered by last name for a tenant
   *
   * @return void
   */
  public function testItReturnsUsersFilteredByLastName()
  {
    $user1 = factory(User::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      "tenant_id" => $tenant2,
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[lastname]={$user1->lastname}")
      ->assertOk()
      ->assertJson([
        "data" => [
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
            "lastname" => $user1->lastname,
          ],
        ],
      ]);
  }

  /**
   * Get all users without and image for a tenant
   *
   * @return void
   */
  public function testItReturnsUsersFilteredWithNoImage()
  {
    $user1 = factory(User::class)->create([
      "firstname" => "james",
      "tenant_id" => $this->user->tenant_id,
    ]);
    $this->user->update(["firstname" => "anthony"]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      "tenant_id" => $tenant2->id,
      "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQi1SYU1kgu3FtGlMpm5W7K2zuZHLgBQZzf34TQ3_Qe8LUd8s5C",
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[has_image]=false")
      ->assertOk()
      ->assertJson([
        "data" => [
          [
            "id" => $this->user->id,
            "firstname" => $this->user->firstname,
          ],
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
          ],
        ],
      ]);
  }

  /**
   * Get all users with images for a tenant
   *
   * @return void
   */
  public function testItReturnsUsersFilteredWithImage()
  {
    $user1 = factory(User::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "image" => "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQi1SYU1kgu3FtGlMpm5W7K2zuZHLgBQZzf34TQ3_Qe8LUd8s5C",
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      "tenant_id" => $tenant2,
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[has_image]=true")
      ->assertOk()
      ->assertJson([
        "data" => [
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
            "image" => $user1->image,
          ],
        ],
      ]);
  }

  /**
   * Filter all users by status for a tenant
   *
   * @return void
   */
  public function testItReturnsUsersFilteredByAccountStatus()
  {
    $user1 = factory(User::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $user1->update([
      "account_status_id" => User::StatusTypes["archived"],
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      "tenant_id" => $tenant2->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[account_status]={$user1->account_status_id}")
      ->assertOk()
      ->assertJson([
        "data" => [
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
          ],
        ],
      ]);
  }

  /**
   * Get a user by id
   *
   * @return void
   */
  public function testItReturnsAUserByUserId()
  {
    $user1 = factory(User::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users/{$user1->id}")
      ->assertStatus(200)
      ->assertJson([
        "id" => $user1->id,
        "firstname" => $user1->firstname,
      ]);
  }

  /**
   * Get a user by id
   *
   * @return void
   */
  public function testItDoesntReturnAnInvalidUser()
  {    
    $this->actingAs($this->user)
      ->getJson("api/v1/users/0")
      ->assertStatus(422);
  }

  /**
   * Update a user
   *
   * @return void
   */
  public function testItUpdatesAValidUser()
  {
    $user1 = factory(User::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->putJson("api/v1/users/{$user1->id}", [
        "firstname" => "melinda",
        "lastname" => "epifano",
      ])
      ->assertOk()
      ->assertJson([
        "id" => $user1->id,
        "firstname" => "melinda",
        "lastname" => "epifano",
      ]);
  }

  /**
   * Update an invalid user
   *
   * @return void
   */
  public function testItDoesntUpdateAnInvalidUser()
  {
    $this->actingAs($this->user)
      ->putJson("api/v1/users/0", [
        "firstname" => "melinda",
        "lastname" => "epifano",
      ])
      ->assertStatus(422);
  }

  /**
   * Create a new user
   *
   * @return void
   */
  public function testItCreatesANewUserWithValidData()
  {
    $data = factory(User::class)->make([
      "tenant_id" => $this->user->tenant->id,
      "address" => [
        "city" => "springfield",
        "country" => "united states of america",
        "state" => "missouri",
        "street" => "1742 evergreen terrace",
      ]
    ]);

    $response = $this->actingAs($this->user)
      ->postJson(
        "api/v1/users/",
        $data->only([
          "address",
          "email",
          "firstname",
          "lastname",
          "tenant_id",
        ])
      )->assertOk();
    
    $user = User::all()->last();

    $response
      ->assertJson([
        "id" => $user->id,
        "firstname" => $user->firstname,
        "lastname" => $user->lastname,
      ]);
  }

  /**
   * Create a new user with invalid data
   *
   * @return void
   */
  public function testItDoesntCreateANewUserWithInvalidData()
  {
    $data = factory(User::class)->make([
      "address" => [
        "city" => "springfield",
        "country" => "united states of america",
        "state" => "missouri",
        "street" => "1742 evergreen terrace",
      ]
    ]);

    $this->actingAs($this->user)
      ->postJson(
        "api/v1/users/",
        $data->only([
          "email",
          "address",
          "lastname",
          "tenant_id",
        ])
        )
        ->assertStatus(422);
  }

  public function testItBatchCreatesNewUsersWithValidData()
  {
    $student1 = factory(User::class)->make([
      "first_name" => "darl",
      "email" => "student1@yopmail.com",
      "tenant_id" => $this->user->tenant->id,
      "student_grade_id" => StudentGrade::first()->id,
    ]);
    $student2 = factory(User::class)->make([
      "first_name" => "darl",
      "email" => "student2@yopmail.com",
      "tenant_id" => $this->user->tenant->id,
      "student_grade_id" => StudentGrade::first()->id,
    ]);

    Bus::fake();

    $this->actingAs($this->user)
      ->postJson(
        "api/v1/users/batch", [
          "type" => "student",
          "data" => [
            $student1->toArray(),
            $student2->toArray(),
          ]
        ])
        ->assertOk();

    $tenant = $this->user->tenant;
    Bus::assertDispatched(function (ProcessBatch $event) use ($tenant) {
      return $event->tenant->id === $tenant->id && $event->type === "student";
    });
  }

  public function testItDoesntBatchCreateNewUsersWithDuplicateEmails()
  {
    $student1 = factory(User::class)->make([
      "email" => "student1@yopmail.com",
      "tenant_id" => $this->user->tenant->id,
      "student_grade_id" => StudentGrade::first()->id,
    ]);
    $student2 = factory(User::class)->make([
      "email" => "student1@yopmail.com",
      "tenant_id" => $this->user->tenant->id,
      "student_grade_id" => StudentGrade::first()->id,
    ]);

    Bus::fake();

    $this->actingAs($this->user)
      ->postJson(
        "api/v1/users/batch", [
          "type" => "student",
          "data" => [
            $student1->toArray(),
            $student2->toArray(),
          ]
        ])
        ->assertStatus(422)
        ->assertJson([
          "message" => "The given data was invalid.",
          "errors" => [
            "data.0.email" => ["The data.0.email field has a duplicate value."],
            "data.1.email" => ["The data.1.email field has a duplicate value."],
          ]
        ]);

    Bus::assertNotDispatched(ProcessBatch::class);
  }

  public function testItDoesntBatchCreateNewUsersWithDuplicateRefId()
  {
    $student1 = factory(User::class)->make([
      "email" => "student1@yopmail.com",
      "tenant_id" => $this->user->tenant->id,
      "ref_id" => "111",
    ]);
    $student2 = factory(User::class)->make([
      "email" => "student2@yopmail.com",
      "tenant_id" => $this->user->tenant->id,
      "ref_id" => "111",
    ]);

    Bus::fake();

    $this->actingAs($this->user)
      ->postJson(
        "api/v1/users/batch", [
          "type" => "student",
          "data" => [
            $student1->toArray(),
            $student2->toArray(),
          ]
        ])
        ->assertStatus(422)
        ->assertJson([
          "message" => "The given data was invalid.",
          "errors" => [
            "data.0.ref_id" => ["The data.0.ref_id field has a duplicate value."],
            "data.1.ref_id" => ["The data.1.ref_id field has a duplicate value."],
          ]
        ]);

    Bus::assertNotDispatched(ProcessBatch::class);
  }
}
