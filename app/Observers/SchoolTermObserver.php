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
    if (is_null($schoolTerm->status_id)) {
      $schoolTerm->status_id = SchoolTerm::Statuses['in progress'];
    }

    if ($schoolTerm->status_id === SchoolTerm::Statuses['in progress']) {
      $schoolTerm->current_term = true;
    }
  }
}
