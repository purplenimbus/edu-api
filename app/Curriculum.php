<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
	public $table = "curricula";
	/**
   * Cast meta property to array
   *
   * @var array
   */
  
	protected $casts = [
    'meta' => 'array',
  ];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'course_grade_id','description','meta','type_id',
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
  ];

  public function scopeOfCourseGrade($query, $course_grade_id)
  {
    return $query->where('course_grade_id', $course_grade_id);
  }

  public function grade()
  {
    return $this->belongsTo('App\CourseGrade','course_grade_id');
  }

  public function subjects()
  {
    return $this->hasMany('App\CurriculumCourseLoad');
  }
  /**
 *  Setup model event hooks
 */
  public static function boot()
  {
    parent::boot();
  }
}
