<?php

use App\Course;
use App\StudentGrade;
use App\CourseStatus;
use App\Instructor;
use App\Student;
use App\Subject;
use App\Tenant;
use Faker\Generator as Faker;

$factory->define(App\Course::class, function (Faker $faker) {
  return [
    'student_grade_id' => factory(StudentGrade::class),
    'description' => $faker->text(200),
    'instructor_id' => factory(Instructor::class),
    'name' => $faker->text(200),
    'status_id' => factory(CourseStatus::class),
    'subject_id' => factory(Subject::class),
    'tenant_id' => factory(Tenant::class),
  ];
});

$factory->define(App\Registration::class, function () {
  return [
    'course_id' => factory(Course::class),
    'user_id' => factory(Student::class),
    'tenant_id' => factory(Tenant::class)
  ];
});

$factory->define(App\Subject::class, function (Faker $faker) {
  return [
    'description' => $faker->text(200),
    'name' => $faker->text(50),
  ];
});

$factory->define(App\StudentGrade::class, function (Faker $faker) {
  return [
    'description' => $faker->text(200),
    'name' => $faker->text(50),
    'tenant_id' => factory(Tenant::class),
  ];
});

$factory->define(App\CourseStatus::class, function (Faker $faker) {
  return [
    'description' => $faker->text(200),
    'name' => $faker->text(50),
  ];
});

$factory->define(App\Lesson::class, function (Faker $faker) {
  return  [
    'title' => $faker->sentence(6, true),
    'description' => $faker->text(200),
    'content' => $faker->paragraphs(3, true)
  ];
});
