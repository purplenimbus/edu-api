<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Course;
use App\Registration;
use App\Student;
use App\Tenant;
use Faker\Generator as Faker;

$factory->define(Registration::class, function (Faker $faker) {
  return [
    'course_id' => factory(Course::class),
    'tenant_id' => factory(Tenant::class),
    'user_id' => factory(Student::class),
  ];
});
