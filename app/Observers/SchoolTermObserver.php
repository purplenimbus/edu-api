<?php

namespace App\Observers;

use App\SchoolTerm;

class SchoolTermObserver
{
  /**
   * Handle the course "creating" event.
   *
   * @param  \App\Course  $course
   * @return void
   */
  public function creating(SchoolTerm $schoolTerm)
  {
    $schoolTerm->status_id = SchoolTerm::Statuses['in progress'];
  }
}
