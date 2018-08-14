<?php

use Faker\Generator as Faker;

$factory->define(App\Tenant::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'username' => (string)$faker->randomNumber(5),
        'email' => (string)$faker->safeEmail()
    ];
});
