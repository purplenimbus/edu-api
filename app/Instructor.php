<?php

namespace App;

use App\User;
use App\Course;
use Bouncer;

class Instructor extends User
{
  public function newQuery($excludeDeleted = true)
  {
    return parent::newQuery($excludeDeleted)
    ->whereIs('instructor');
  }

  /**
   *  Setup model event hooks
  */
  public static function boot()
  {
    parent::boot();
    self::creating(function ($model) {
      $model->password = $model->createDefaultPassword();
      $status_type = StatusType::where('name', 'created')->first();

      if (!is_null($status_type)) {
        $model->account_status_id = $status_type->id;
      }

    });

    self::created(function ($model) {
      $model->assign('instructor');     
    });
  }

  /**
   *  Assign Instructor
  */
  public function assignInstructor(Course $course) 
  {
    $course->fill([
      'instructor_id' => $this->id,
    ]);

    $course->save();

    $this->allow('edit', $course);
    $this->allow('view', $course);

    return $course;
  }
}
