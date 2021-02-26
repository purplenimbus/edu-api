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
    ]
  ],
  'pagination' => 10
];
