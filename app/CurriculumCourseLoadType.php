<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurriculumCourseLoadType extends Model
{
  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'created_at','updated_at'
  ];

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
