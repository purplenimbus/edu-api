<?php

namespace Tests\Unit;

use App\Guardian;
use App\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

class WardControllerTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;

  public function testItReturnsPaginatedWards() {
    $student1 = factory(Student::class)->create([
      "firstname" => "zero",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian1 = factory(Guardian::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian1->assignWards([
      $student1->id,
      $student2->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/guardians/{$guardian1->id}/wards")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          ["user_id" => $student1->id],
          ["user_id" => $student2->id]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  public function testItReturnsPaginatedWardsFilteredByFirstName() {
    $student1 = factory(Student::class)->create([
      "firstname" => "zero",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian1 = factory(Guardian::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian1->assignWards([
      $student1->id,
      $student2->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/guardians/{$guardian1->id}/wards?filter[firstname]=anthony")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          ["user_id" => $student2->id]
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  public function testItReturnsPaginatedWardsFilteredByLastName() {
    $student1 = factory(Student::class)->create([
      "lastname" => "zero",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian1 = factory(Guardian::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian1->assignWards([
      $student1->id,
      $student2->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/guardians/{$guardian1->id}/wards?filter[lastname]=zero")
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          ["user_id" => $student1->id]
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }
}