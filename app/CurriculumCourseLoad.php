<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurriculumCourseLoad extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'curriculum_id',
    'subject_id',
    'type_id',
  ];
  /**
   * Get course registrations
   *
   * @var array
   */
  public function curriculum()
  {
    return $this->belongsTo('App\Curriculum');
  }

  public function subject()
  {
    return $this->belongsTo('App\Subject');
  }

  /**
   * Get course registrations
   *
   * @var array
   */
  public function type()
  {
    return $this->hasOne('App\CurriculumCourseLoadType', 'id', 'type_id');
  }
}
