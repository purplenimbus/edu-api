<?php

namespace Tests\Unit;

use App\Course;
use App\Instructor;
use App\NimbusEdu\Institution;
use App\Registration;
use App\Student;
use App\StudentGrade;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

class InstructorControllerTest extends TestCase
{
  use SetupUser, RefreshDatabase, WithoutMiddleware;

  public function testItReturnsPaginatedInstructorsSortedByName()
  {
    $instructor1 = factory(Instructor::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $instructor2 = factory(Instructor::class)->create([
      "firstname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10")
      ->assertJson([
      "current_page" => 1,
      "data" => [
        [ "id" => $instructor1->id ],
        [ "id" => $instructor2->id ]
      ],
      "per_page" => 10,
      "total" => 2,
    ]);
  }

  public function testItReturnsPaginatedInstructorsSortedByCreatedAtInAscendingOrder()
  {
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $instructor2 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "created_at" => Carbon::now()->tomorrow()
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&sort=created_at")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $instructor1->id,
          ],
          [ 
            "id" => $instructor2->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  public function testItReturnsPaginatedInstructorsSortedByCreatedAtInDesendingOrder()
  {
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $instructor2 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "created_at" => Carbon::now()->tomorrow()
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&sort=-created_at")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $instructor2->id,
          ],
          [ 
            "id" => $instructor1->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  public function testItReturnsPaginatedInstructorsSortedByUpdatedAtInAscendingOrder()
  {
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $instructor2 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "updated_at" => Carbon::now()->tomorrow()
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&sort=updated_at")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $instructor1->id,
          ],
          [ 
            "id" => $instructor2->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  public function testItReturnsPaginatedInstructorsSortedByUpdatedAtInDesendingOrder()
  {
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $instructor2 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "updated_at" => Carbon::now()->tomorrow()
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&sort=-updated_at")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $instructor2->id,
          ],
          [ 
            "id" => $instructor1->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  public function testItReturnsPaginatedInstructorsFilteredByFirstName()
  {
    $instructor1 = factory(Instructor::class)->create([
      "firstname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Instructor::class)->create([
      "firstname" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&filter[firstname]=diana")->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $instructor1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  public function testItReturnsPaginatedInstructorsFilteredByLastName()
  {
    $instructor1 = factory(Instructor::class)->create([
      "lastname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Instructor::class)->create([
      "lastname" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&filter[lastname]=diana")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $instructor1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  public function testItReturnsPaginatedInstructorsFilteredByEmail()
  {
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&filter[email]=$instructor1->email")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $instructor1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  public function testItReturnsPaginatedInstructorsFilteredByImage()
  {
    $instructor1 = factory(Instructor::class)->create([
      "image" => "http://www.thisisanimage.com",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&filter[has_image]=true")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $instructor1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  public function testItReturnsPaginatedInstructorsWithOutImage()
  {
    factory(Instructor::class)->create([
      "image" => "http://www.thisisanimage.com",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&filter[has_image]=false")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $instructor1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  public function testItReturnsPaginatedInstructorsFilteredByAccountStatus()
  {
    $accountStatus = Student::StatusTypes['registered'];
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $instructor1->update(['account_status_id' => $accountStatus]);
    factory(Instructor::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/instructors?paginate=10&filter[account_status]=$accountStatus")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $instructor1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  public function testItUpdatesAValidInstructor() {
    $instructor = factory(Instructor::class)->create([
      'firstname' => 'johnny',
      'tenant_id' => $this->user->tenant->id,
    ]);
    $this->actingAs($this->user)
      ->putJson("api/v1/instructors/$instructor->id", [
        'firstname' => 'english',
      ])
      ->assertOk()
      ->assertJson([
        'id' => $instructor->id,
        'firstname' => 'english',
      ]);
  }

  public function testItDoesntUpdateAnInvalidInstructor() {
    $this->actingAs($this->user)
      ->putJson("api/v1/instructors/0", [
        'firstname' => 'english',
      ])
      ->assertStatus(422);
  }

  public function testItCreatesAValidInstructor() {
    $data = factory(Instructor::class)->make([
      'firstname' => 'english',
      'tenant_id' => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->postJson("api/v1/instructors", $data->toArray())
      ->assertOk()
      ->assertJson([
        'email' => $data->email,
        'firstname' => 'english',
      ]);
  }

  public function testItDoesntCreateAnInstructorFromInvalidData() {
    $data = factory(Instructor::class)->make([
      'firstname' => 'english',
      'tenant_id' => 0,
    ]);
    $data->tenant_id = 0;

    $this->actingAs($this->user)
      ->postJson("api/v1/instructors", $data->toArray())
      ->assertStatus(422);
  }

  public function testItShowsAValidInstructor() {
    $instructor = factory(Instructor::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $this->actingAs($this->user)
      ->getJson("api/v1/instructors/$instructor->id")
      ->assertOk()
      ->assertJson([
        'email' => $instructor->email,
        'id' => $instructor->id,
      ]);
  }

  public function testItDoesntShowAnInvalidInstructor() {
    $this->actingAs($this->user)
      ->getJson("api/v1/instructors/0")
      ->assertStatus(422);
  }
}
