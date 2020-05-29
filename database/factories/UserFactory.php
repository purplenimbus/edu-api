<?php

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

$factory->define(App\User::class, function (Faker $faker) {
  return [
    'address' => [
      'street' => $faker->streetAddress
    ],
    'date_of_birth' => $faker->date,
    'firstname' => $faker->name,
    'lastname' => $faker->name,
    'email' => $faker->email,
    'password' => app('hash')->make('123456'),
		//'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQi1SYU1kgu3FtGlMpm5W7K2zuZHLgBQZzf34TQ3_Qe8LUd8s5C',
  ];
});

$factory->defineAs(App\Student::class, 'student', function (Faker $faker) use ($factory) {
  return $factory->raw(App\User::class);
});

$factory->defineAs(App\Instructor::class, 'teacher', function (Faker $faker) use ($factory) {
  return $factory->raw(App\User::class);
});

$factory->defineAs(App\Guardian::class, 'parent', function (Faker $faker) use ($factory) {
  return $factory->raw(App\User::class);
});