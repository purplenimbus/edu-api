<?php

namespace Tests\Unit;

use App\Student;
use App\StudentGrade;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

class StudentControllerTest extends TestCase
{
  use SetupUser, RefreshDatabase, WithoutMiddleware;
  /**
   * Return students sorted by first name.
   *
   * @return void
   */
  public function testStudentIndexSortedByName()
  {
    $student1 = factory(Student::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "firstname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students");
    
    $response->assertJson([
      "current_page" => 1,
      "data" => [
        [ "id" => $student1->id ],
        [ "id" => $student2->id ]
      ],
      "per_page" => 10,
      "total" => 2,
    ]);
  }

  /**
   * Returns students sorted by created_at in asending order
   *
   * @return void
   */
  public function testPaginatedStudentIndexSortedByCreatedAtInAsendingOrder()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "created_at" => Carbon::now()->tomorrow()
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?sort=created_at");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $student1->id,
          ],
          [ 
            "id" => $student2->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns students sorted by created_at in desending order
   *
   * @return void
   */
  public function testPaginatedStudentIndexSortedByCreatedAtInDesendingOrder()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "created_at" => Carbon::now()->tomorrow()
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?sort=-created_at");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $student2->id,
          ],
          [ 
            "id" => $student1->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns students sorted by updated_at in asending order
   *
   * @return void
   */
  public function testPaginatedStudentIndexSortedByUpdatedAtInAsendingOrder()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "updated_at" => Carbon::now()->tomorrow()
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?sort=updated_at");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $student1->id,
          ],
          [ 
            "id" => $student2->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns students sorted by updated_at in desending order
   *
   * @return void
   */
  public function testPaginatedStudentIndexSortedByUpdatedAtInDesendingOrder()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student2 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
      "updated_at" => Carbon::now()->tomorrow()
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?sort=-updated_at");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ 
            "id" => $student2->id,
          ],
          [ 
            "id" => $student1->id,
          ]
        ],
        "per_page" => 10,
        "total" => 2,
      ]);
  }

  /**
   * Returns students filtered by firstname
   *
   * @return void
   */
  public function testPaginatedStudentIndexFilteredByFirstName()
  {
    $student1 = factory(Student::class)->create([
      "firstname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "firstname" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[firstname]=diana");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students filtered by firstname
   *
   * @return void
   */
  public function testPaginatedStudentIndexFilteredByLastName()
  {
    $student1 = factory(Student::class)->create([
      "lastname" => "diana",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "lastname" => "english",
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[lastname]=diana");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students filtered by email
   *
   * @return void
   */
  public function testPaginatedStudentIndexFilteredByEmail()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[email]=$student1->email");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students filtered by student id
   *
   * @return void
   */
  public function testPaginatedStudentIndexFilteredByStudentId()
  {
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[student_id]=$student1->student_id");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students filtered by image
   *
   * @return void
   */
  public function testPaginatedStudentIndexFilteredByImage()
  {
    $student1 = factory(Student::class)->create([
      "image" => "http://www.thisisanimage.com",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[has_image]=true");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students with out an image
   *
   * @return void
   */
  public function testPaginatedStudentIndexWithOutImage()
  {
    factory(Student::class)->create([
      "image" => "http://www.thisisanimage.com",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[has_image]=false");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns students by student grade
   *
   * @return void
   */
  public function testPaginatedStudentFilteredByStudentGrade()
  {
    $studentGrade1 = StudentGrade::whereAlias('js 1')->first();
    $studentGrade2 = StudentGrade::whereAlias('ss 3')->first();
    factory(Student::class)->create([
      "meta" => [ "student_grade_id" => $studentGrade1->id ],
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student1 = factory(Student::class)->create([
      "meta" => [ "student_grade_id" => $studentGrade2->id ],
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[student_grade_id]=$studentGrade2->id");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }

  /**
   * Returns filtered by account status
   *
   * @return void
   */
  public function testPaginatedStudentFilteredByAccountStatus()
  {
    $accountStatus = Student::StatusTypes['registered'];
    $student1 = factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $student1->update(['account_status_id' => $accountStatus]);
    factory(Student::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/students?filter[account_status]=$accountStatus");
    
    $response
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "data" => [
          [ "id" => $student1->id ],
        ],
        "per_page" => 10,
        "total" => 1,
      ]);
  }
}
