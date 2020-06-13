<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurriculumCourseLoadType extends Model
{
  /**
   * Get course registrations
   *
   * @var array
   */
  public function curriculum()
  {
    return $this->belongsTo('App\CurriculumCourseLoad', 'type_id');
  }
}
