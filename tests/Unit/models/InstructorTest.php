<?php

namespace Tests\Unit\Models;

use App\Course;
use App\Instructor;
use App\Tenant;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PermissionsSeeder;
use Tests\TestCase;

class InstructorTest extends TestCase
{
  use RefreshDatabase;

  public function testItHasManyCourses() {
    $tenant = factory(Tenant::class)->create();
    $instructor = factory(Instructor::class)->create([
      "tenant_id" => $tenant->id,
    ]);
    $course1 = factory(Course::class)->create([
      "instructor_id" => $instructor->id,
      "tenant_id" => $tenant->id,
    ]);
    $course2 = factory(Course::class)->create([
      "created_at" => Carbon::now()->addDay(1),
      "instructor_id" => $instructor->id,
      "tenant_id" => $tenant->id,
    ]);

    $this->assertEquals($course1->id, $instructor->courses->first()->id);
    $this->assertEquals($course2->id, $instructor->courses()->latest()->first()->id);
    $this->assertEquals(2, $instructor->courses->count());
  }

  public function testItsScopedToATenant() {
    $tenant1 = factory(Tenant::class)->create();
    $tenant2 = factory(Tenant::class)->create();
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $tenant1->id,
    ]);
    $instructor2 = factory(Instructor::class)->create([
      "tenant_id" => $tenant2->id,
    ]);

    $this->assertEquals(1, $instructor1->ofTenant($tenant1->id)->count());
    $this->assertEquals(1, $instructor2->ofTenant($tenant2->id)->count());
  }

  public function testItAssignsAnInstructorToACourse() {
    $tenant = factory(Tenant::class)->create();
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $tenant->id,
    ]);
    $instructor2 = factory(Instructor::class)->create([
      "tenant_id" => $tenant->id,
    ]);
    $course = factory(Course::class)->create([
      "instructor_id" => $instructor1->id,
      "tenant_id" => $tenant->id,
    ]);

    $this->assertEquals($instructor1->id, $course->instructor->id);
    $instructor2->assignInstructor($course);
    $this->assertEquals($instructor2->id, $course->refresh()->instructor->id);
  }

  public function testItSetsCoursePermissions() {
    $this->seed(PermissionsSeeder::class);
    $tenant = factory(Tenant::class)->create();
    $instructor1 = factory(Instructor::class)->create([
      "tenant_id" => $tenant->id,
    ]);
    $instructor2 = factory(Instructor::class)->create([
      "tenant_id" => $tenant->id,
    ]);
    $course = factory(Course::class)->create([
      "instructor_id" => $instructor1->id,
      "tenant_id" => $tenant->id,
    ]);

    $instructor1->setCoursePermissions($course);
    $this->assertTrue($instructor1->can("view", $course));
    $instructor2->setCoursePermissions($course);
    \Bouncer::refreshFor($instructor1);

    $this->assertFalse($instructor1->refresh()->can("view", $course));
    $this->assertTrue($instructor2->can("view", $course));
  }
}
