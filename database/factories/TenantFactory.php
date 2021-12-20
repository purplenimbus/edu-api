<?php

use App\Tenant;
use Faker\Generator as Faker;

$factory->define(Tenant::class, function (Faker $faker) {
  return [
    'address' => [
      'city' => 'uyo',
      'street' => $faker->streetAddress,
      'state' => 'akwa ibom',
      'country' => 'nigeria'
    ],
    'name' => $faker->company,
  ];
});
