<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SchoolTermStatus;
use App\SchoolTermType;
use App\Tenant;
use Faker\Generator as Faker;

$factory->define(SchoolTermType::class, function (Faker $faker) {
  return [
    'description' => $faker->text(200),
    'end_date' => config('edu.default.school_terms.0.end_date'),
    'name' => $faker->text(50),
    'start_date' => config('edu.default.school_terms.0.start_date'),
    'tenant_id' => factory(Tenant::class),
  ];
});

$factory->define(SchoolTermStatus::class, function (Faker $faker) {
  return [
    'description' => $faker->text(200),
    'name' => $faker->text(50),
  ];
});
