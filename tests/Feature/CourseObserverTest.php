<?php

namespace Tests\Feature;

use App\CourseGrade;
use App\Nimbus\Institution;
use App\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Bouncer;
use CoursesSeeder;
use PermissionsSeeder;
use Tests\Feature\Helpers\Auth\SetupUser;
use Tests\TestCase;

class CourseObserverTest extends TestCase
{
  use RefreshDatabase, WithFaker, SetupUser;
  /**
   * A basic feature test example.
   *
   * @return void
   */
  public function testSetDefaultCourseAttributes()
  {
    $this->seed(PermissionsSeeder::class);
    $this->user->assign('admin');
    $this->user->allow('edit-courses');
    $institution = new Institution($this->user->tenant);
    $institution->generateSubjects();
    $institution->generateClasses();

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/courses", [
        "course_grade_id" => 1,
        "subject_id" => 1,
      ]);

    $response->assertStatus(200);
  }
}
