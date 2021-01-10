<?php

use App\Tenant;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Tenant::class, function (Faker $faker) {
  return [
    'name' => $faker->company,
  ];
});
