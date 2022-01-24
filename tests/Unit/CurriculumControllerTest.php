<?php

use App\Course;
use App\CurriculumCourseLoad;
use App\NimbusEdu\Institution;
use App\StudentGrade;
use App\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\SetupUser;
use Tests\TestCase;

class CurriculumControllerTest extends TestCase
{
  use SetupUser, RefreshDatabase;

  public function testItReturnsAListOfValidSubjects()
  {
    $this->seed(SubjectsSeeder::class);

    $this->actingAs($this->user)
      ->getJson("api/v1/subjects")
      ->assertOk();
  }

  public function testItReturnsAValidSubjectForAValidSubjectId()
  {
    $this->seed(SubjectsSeeder::class);

    $this->actingAs($this->user)
      ->getJson("api/v1/subjects?subject_id=" . Subject::first()->id)
      ->assertOk()
      ->assertJson(Subject::first()->toArray());
  }

  public function testItDoesntReturnAValidSubjectForAnInvalidSubjectId()
  {
    $this->seed(SubjectsSeeder::class);

    $this->actingAs($this->user)
      ->getJson("api/v1/subjects?subject_id=0")
      ->assertStatus(422)
      ->assertJson([
        "message" => "The given data was invalid.",
        "errors" => [
          "subject_id" => [
            "The selected subject id is invalid."
          ]
        ]
      ]);
  }

  public function testItReturnsAListOfClasses()
  {
    $this->actingAs($this->user)
      ->getJson("api/v1/grades/list")
      ->assertOk()
      ->assertJson(StudentGrade::ofTenant($this->user->tenant_id)->get(['alias', 'description', 'id', 'name'])->toArray());
  }

  public function testItDosentReturnACourseLoadForAnInvalidStudentGrade()
  {
    $this->actingAs($this->user)
      ->getJson("api/v1/curriculum?student_grade_id=0")
      ->assertStatus(422)
      ->assertJson([
        "message" => "The given data was invalid.",
        "errors" => [
          "student_grade_id" => [
            "The selected student grade id is invalid."
          ]
        ]
      ]);
  }

  public function testItReturnsACourseLoadForAValidStudentGrade()
  {
    $this->seed(SubjectsSeeder::class);
    $this->seed(OtherSeeders::class);
    $institution = new Institution();
    $institution->generateCurriculum($this->user->tenant);
    $studentGrade = StudentGrade::first();

    $this->actingAs($this->user)
      ->getJson("api/v1/curriculum?student_grade_id=" . $studentGrade->id)
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "total" => 14,
      ]);
  }

  public function testItReturnsACourseLoadForAValidStudentGradeFilteredByTypeId()
  {
    $this->seed(SubjectsSeeder::class);
    $this->seed(OtherSeeders::class);
    $institution = new Institution();
    $institution->generateCurriculum($this->user->tenant);
    $query = http_build_query([
      "student_grade_id" => StudentGrade::whereAlias('js 1')->first()->id,
      "filter[type_id]" => CurriculumCourseLoad::Types["core"],
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/curriculum?" . $query)
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "total" => 19,
      ]);
  }

  public function testItReturnsACourseLoadForAValidStudentGradeFilteredByType()
  {
    $this->seed(SubjectsSeeder::class);
    $this->seed(OtherSeeders::class);
    $institution = new Institution();
    $institution->generateCurriculum($this->user->tenant);
    $studentGrade = StudentGrade::whereAlias('js 1')->first();
    $query = http_build_query([
      "student_grade_id" => $studentGrade->id,
      "filter[type]" => array_flip(CurriculumCourseLoad::Types)[1],
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/curriculum?" . $query)
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "total" => 19,
      ]);
  }

  public function testItAppendsHasCourseProperty()
  {
    $this->seed(SubjectsSeeder::class);
    $this->seed(OtherSeeders::class);
    $institution = new Institution();
    $institution->generateCurriculum($this->user->tenant);
    $institution->newSchoolTerm($this->user->tenant, 'first term');
    $studentGrade = StudentGrade::whereAlias('js 1')->first();
    $subject = Subject::whereCode('MATH')->first();
    factory(Course::class)->create([
      'subject_id' => $subject->id,
      'student_grade_id' => $studentGrade->id,
      'tenant_id' => $this->user->tenant_id,
      'term_id' => $this->user->tenant->current_term->id,
    ]);
    $query = http_build_query([
      "append" => "has_course",
      "filter[type]" => array_flip(CurriculumCourseLoad::Types)[1],
      "page" => 1,
      "student_grade_id" => $studentGrade->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/curriculum?" . $query)
      ->assertOk()
      ->assertJson([
        "current_page" => 1,
        "total" => 19,
      ]);

    $collection = collect($response->json()["data"]);

    $this->assertTrue(
      $collection->contains(function($value) { return $value["has_course"]; })
    );
  }
}
