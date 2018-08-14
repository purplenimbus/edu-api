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
        'firstname' => $faker->name,
        'lastname' => $faker->name,
        'email' => $faker->email,
        'password' => app('hash')->make('123456'),
		'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQi1SYU1kgu3FtGlMpm5W7K2zuZHLgBQZzf34TQ3_Qe8LUd8s5C',
		'access_level' => 1
    ];
});

$factory->defineAs(App\User::class, 'student', function (Faker $faker) use ($factory) {
    $user = $factory->raw(App\User::class);

	return array_merge($user, ["meta" => [ "user_type" => "student" , "business_unit" => "school" ,  "address" => ["street" => $faker->streetAddress]]]);
});

$factory->defineAs(App\User::class, 'teacher', function (Faker $faker) use ($factory) {
    $user = $factory->raw(App\User::class);
	
    return array_merge($user, ["meta" => [ "user_type" => "teacher" , "business_unit" => "school","address" => ["street" => $faker->streetAddress]]]);
});

$factory->defineAs(App\User::class, 'admin', function (Faker $faker) use ($factory) {
    $user = $factory->raw(App\User::class);
	
    return array_merge($user, ["access_level" => 3]);
});