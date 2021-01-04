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
   *  Assign Instructor
  */
  public function assignInstructor(Course $course) 
  { 
    $former_instructor_id = $course->getOriginal()["instructor_id"];
    $former_instructor = Instructor::find($former_instructor_id);

    if ($former_instructor) {
      Bouncer::disallow($former_instructor)->to('view', $course);
      Bouncer::disallow($former_instructor)->to('edit', $course);
    }

    Bouncer::allow($this)->to('edit', $course);
    Bouncer::allow($this)->to('view', $course);
  }

  /**
   * Get instructor courses
   *
   * @var array
   */
  public function courses()
  {
    return $this->hasMany('App\Course','instructor_id','id');
  }

  public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }
}
