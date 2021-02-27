<?php

use Carbon\Carbon;

return [
  'default' => [
    'country' => 'nigeria',
    'course_schema' => [
      [
        'name' => 'midterm 1',
        'score' => 20,
      ],
      [
        'name' => 'midterm 2',
        'score' => 20,
      ],
      [
        'name' => 'midterm 3',
        'score' => 20,
      ],
      [
        'name' => 'exam',
        'score' => 40,
      ]
    ],
    'school_terms' => [
      [
        'end_date' => Carbon::createFromDate(null, 12, 16)->toDateString(),
        'name' => 'first term',
        'start_date' => Carbon::createFromDate(null, 12, 9)->toDateString(),
      ],
      [
        'end_date' => Carbon::createFromDate(null, 4, 6)->toDateString(),
        'name' => 'second term',
        'start_date' => Carbon::createFromDate(null, 1, 6)->toDateString(),
      ],
      [
        'end_date' => Carbon::createFromDate(null, 7, 20)->toDateString(),
        'name' => 'third term',
        'start_date' => Carbon::createFromDate(null, 4, 20)->toDateString(),
      ]
    ],
    'payment_item_types' => [
      [
        'name' => 'tuition',
      ],
      [
        'name' => 'administrative',
      ],
    ],
    'student_grades' => [
      [
        'alias' => 'primary 1',
        'name' => 'primary 1',
      ],
      [
        'alias' => 'primary 2',
        'name' => 'primary 2',
      ],
      [
        'alias' => 'primary 3',
        'name' => 'primary 3',
      ],
      [
        'alias' => 'primary 4',
        'name' => 'primary 4',
      ],
      [
        'alias' => 'primary 5',
        'name' => 'primary 5',
      ],
      [
        'alias' => 'primary 6',
        'name' => 'primary 6',
      ],
      [
        'alias' => 'js 1',
        'name' => 'junior secondary 1',
      ],
      [
        'alias' => 'js 2',
        'name' => 'junior secondary 2',
      ],
      [
        'alias' => 'js 3',
        'name' => 'junior secondary 3',
      ],
      [
        'alias' => 'ss 1',
        'name' => 'senior secondary 1',
      ],
      [
        'alias' => 'ss 2',
        'name' => 'senior secondary 2',
      ],
      [
        'alias' => 'ss 3',
        'name' => 'senior secondary 3',
      ],
      [
        'alias' => 'a level',
        'name' => 'advanced level',
      ],
    ],
  ],
  'pagination' => 10
];
