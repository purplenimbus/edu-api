<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\StudentGrade;
use App\PaymentProfile;
use App\PaymentProfileItem;
use App\PaymentProfileItemType;
use App\SchoolTermType;
use App\Tenant;
use Faker\Generator as Faker;

$factory->define(PaymentProfile::class, function (Faker $faker) {
  return [
    'student_grade_id' => factory(StudentGrade::class),
    'description' => $faker->text(200),
    'name' => $faker->text(50),
    'tenant_id' => factory(Tenant::class),
    'school_term_type_id' => factory(SchoolTermType::class),
  ];
});

$factory->define(PaymentProfileItem::class, function (Faker $faker) {
  return [
    'amount' => $faker->numberBetween(5000, 20000),
    'description' => $faker->text(200),
    'payment_profile_id' => factory(PaymentProfileItem::class),
    'tenant_id' => factory(Tenant::class),
    'type' => PaymentProfileItem::Types[$faker->numberBetween(0, count(PaymentProfileItem::Types) - 1)],
  ];
});

$factory->define(PaymentProfileItemType::class, function (Faker $faker) {
  return [
    'description' => $faker->text(200),
    'name' => $faker->text(50),
    'tenant_id' => factory(Tenant::class),
  ];
});