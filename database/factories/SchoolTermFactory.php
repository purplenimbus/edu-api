<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SchoolTerm;
use App\SchoolTermStatus;
use App\SchoolTermType;
use App\Tenant;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(SchoolTerm::class, function (Faker $faker) {
	return [
		'description' => $faker->text(200),
    'end_date' => Carbon::now()->addMonths(4),
    'name' => $faker->text(200),
    'start_date' => Carbon::now(),
    'status_id' => factory(SchoolTermStatus::class),
    'tenant_id' => factory(Tenant::class),
    'type_id' => factory(SchoolTermType::class),
	];
});
