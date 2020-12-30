<?php

use Illuminate\Database\Seeder;

class OtherSeeders extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $currencies = [
      [
        'shortname' => 'NGN',
        'longname'  => 'naira',
        'symbol'  =>  '₦'
      ]
    ];
    
    //Create Currencies
    foreach($currencies as $currency){
      App\Currency::create($currency);
    }

    $curricula_types = [
      [ 'country' => 'nigeria'  ],
    ];
    
    //Create Curriculla Types
    foreach($curricula_types as $curricula_type){
      App\CurriculumType::create($curricula_type);
    }

    $account_status_types = [
      [ 'name' => 'created' ],
      [ 'name' => 'unenrolled'  ],
      [ 'name' => 'registered'  ],
      [ 'name' => 'assigned'  ],
      [ 'name' => 'terminated'  ],
      [ 'name' => 'archived'  ],
    ];
    
    //Create Account Status Types
    foreach($account_status_types as $account_status_type){
      App\StatusType::create($account_status_type);
    }

    $invoice_status_types = [
      [ 'name' => 'pending' ],
      [ 'name' => 'past_due' ],
      [ 'name' => 'paid'  ],
      [ 'name' => 'voided' ],
      [ 'name' => 'archived'  ],
    ];
    
    //Create Invoice Status Types
    foreach($invoice_status_types as $billing_status_type){
      App\InvoiceStatus::create($billing_status_type);
    }

    $transaction_statuses = [
      [ 'name' => 'pending' ],
      [ 'name' => 'success' ],
      [ 'name' => 'failed' ],
      [ 'name' => 'refunded'  ],
    ];
    
    //Create Invoice Statuses
    foreach($transaction_statuses as $transaction_status){
      App\TransactionStatus::create($transaction_status);
    }

    $course_statuses = [
      [ 'name' => 'created' ],
      [ 'name' => 'in progress' ],
      [ 'name' => 'complete' ],
      [ 'name' => 'archived'  ],
    ];
    
    //Create Course Statues
    foreach($course_statuses as $course_status){
      App\CourseStatus::create($course_status);
    }

    $term_status_types = [
      [ 'name' => 'in progress' ],
      [ 'name' => 'complete'  ],
      [ 'name' => 'archived'  ],
    ];

    //Create School Term Status Types
    foreach($term_status_types as $status_type) {
      App\SchoolTermStatus::create($status_type);
    }

    $course_load_types = [
      [ 'name' => 'core' ],
      [ 'name' => 'elective'  ],
      [ 'name' => 'optional'  ],
    ];

    //Create Curriculum Course Load Types
    foreach($course_load_types as $type){
      App\CurriculumCourseLoadType::create($type);
    }

    $user_group_types = [
      [ 'name' => 'family' ],
      [ 'name' => 'class list' ],
    ];

    //Create User Group Types
    foreach($user_group_types as $type){
      App\UserGroupType::create($type);
    }
  }
}
