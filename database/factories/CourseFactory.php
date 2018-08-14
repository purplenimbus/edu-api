<?php

use Faker\Generator as Faker;

$factory->define(App\Course::class, function (Faker $faker) {
    return [
        'description' => $faker->text(200)
    ];
});

$factory->define(App\Scores::class, function (Faker $faker) {
	
    return  [
			'quiz' => $faker->numberBetween(0,15),
			'midterm' => $faker->numberBetween(0,30),
			'assignment' => $faker->numberBetween(0,15),
			'lab' => $faker->numberBetween(0,5),
			'exam' => $faker->numberBetween(0,35)
		];
});

$factory->define(App\Lesson::class, function (Faker $faker) {
	
    return  [
			'title' => $faker->sentence(6,true),
			'description' => $faker->text(200),
			'content' => $faker->paragraphs(3,true)
		];
});
