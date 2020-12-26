<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BankAccount;
use App\Tenant;
use Faker\Generator as Faker;

$factory->define(BankAccount::class, function (Faker $faker) {
  return [
    "account_name" => $faker->name,
    "account_number" => $faker->numberBetween(3845000000, 3849999999),
    "bank_code" => $faker->numberBetween(1, 100),
    "bank_name" => $faker->company,
    "description" => $faker->text,
    "tenant_id" => factory(Tenant::class),
  ];
});
