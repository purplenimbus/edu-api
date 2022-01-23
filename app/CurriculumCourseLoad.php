<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class CurriculumCourseLoad extends Model
{
  const Types = [
    "core" => 1,
    "elective" => 2,
    "optional" => 3,
  ];
  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'created_at','updated_at'
  ];

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

  public function curriculum()
  {
    return $this->belongsTo('App\Curriculum');
  }

  public function subject()
  {
    return $this->belongsTo('App\Subject');
  }

  public function type()
  {
    return $this->hasOne('App\CurriculumCourseLoadType', 'id', 'type_id');
  }

  public function getHasCourseAttribute()
  {
    $query = Course::whereSubjectId($this->subject->id)
      ->ofStudentGrade($this->curriculum->grade->id);

    $currentTerm = Arr::get($this, 'curriculum.student_grade.tenant.current_term', null);

    if (!is_null($currentTerm)) {
      $query->ofSchoolTerm($currentTerm->id);
    }

    return $query->first() ? true : false;
  }

  public function scopeOfCore($query)
  {
    return $query->where('type_id', self::Types["core"]); //need to move to constant
  }
}
