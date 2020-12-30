<?php

use App\Guardian;
use App\Instructor;
use App\Student;
use App\Tenant;
use App\User;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
  return [
    'address' => [
      'street' => $faker->streetAddress
    ],
    'date_of_birth' => $faker->date,
    'firstname' => $faker->name,
    'lastname' => $faker->name,
    'email' => $faker->email,
    'password' => '123456',
    'tenant_id' => factory(Tenant::class),
		//'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQi1SYU1kgu3FtGlMpm5W7K2zuZHLgBQZzf34TQ3_Qe8LUd8s5C',
  ];
});

$factory->define(Student::class, function () use ($factory) {
  return $factory->raw(User::class);
});

$factory->define(Instructor::class, function () use ($factory) {
  return $factory->raw(User::class);
});

$factory->define(Guardian::class, function () use ($factory) {
  return $factory->raw(User::class);
});