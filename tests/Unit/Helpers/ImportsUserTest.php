<?php

namespace Tests\Unit\Helpers;

use App\Guardian;
use App\Instructor;
use App\NimbusEdu\Helpers\importsUser;
use App\Student;
use App\StudentGrade;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportsUserTest extends TestCase
{
  use importsUser, RefreshDatabase;

  public function testItImportsAStudent() {
    $tenant = factory(Tenant::class)->create();
    $student = factory(Student::class)->make([
      "email" => "student1@yopmail.com",
      "tenant_id" => $tenant->id,
      "student_grade_id" => StudentGrade::first()->id,
    ])->toArray();

    $this->importStudent($student, $tenant);
    $this->assertEquals(Student::latest()->first()->email, "student1@yopmail.com");
    $this->assertEquals(Student::latest()->first()->grade["id"], StudentGrade::first()->id);
  }

  public function testItImportsAnInstructor() {
    $tenant = factory(Tenant::class)->create();
    $instructor = factory(Instructor::class)->make([
      "email" => "instructor1@yopmail.com",
      "tenant_id" => $tenant->id,
    ])->toArray();

    $this->importInstructor($instructor, $tenant);
    $this->assertEquals(Instructor::latest()->first()->email, "instructor1@yopmail.com");
  }

  public function testItImportsAGuardian() {
    $tenant = factory(Tenant::class)->create();
    $guardian = factory(Guardian::class)->make([
      "email" => "guardian1@yopmail.com",
      "tenant_id" => $tenant->id,
    ])->toArray();

    $this->importGuardian($guardian, $tenant);
    $this->assertEquals(Guardian::latest()->first()->email, "guardian1@yopmail.com");
  }
}