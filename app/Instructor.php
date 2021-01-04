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

    // self::created(function ($model) {
    //   $user = User::find($model->id);
    //   $user->assign('instructor');//Assign user model a role to return roles and permissions for JWT Claims
    //   $model->assign('instructor');
    // });
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
