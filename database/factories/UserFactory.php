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
      'city' => 'uyo',
      'street' => $faker->streetAddress,
      'state' => 'akwa ibom',
      'country' => 'nigeria'
    ],
    'date_of_birth' => $faker->date,
    'firstname' => $faker->firstName,
    'lastname' => $faker->lastName,
    'email' => $faker->email,
    'password' => '123456',
    'tenant_id' => factory(Tenant::class),
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