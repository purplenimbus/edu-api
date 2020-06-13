<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurriculumCourseLoad extends Model
{
  /**
   * Get course registrations
   *
   * @var array
   */
  public function curriculum()
  {
    return $this->belongsTo('App\Curriculum');
  }

  /**
   * Get course registrations
   *
   * @var array
   */
  public function type()
  {
    return $this->hasOne('App\CurriculumCourseLoadType');
  }
}
