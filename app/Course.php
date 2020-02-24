<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Webpatser\Uuid\Uuid as Uuid;

class Course extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name','description', 'meta','tenant_id','instructor_id','subject_id','code','course_grade_id', 'schema'
  ];

  /**
   * Cast meta property to array
   *
   * @var array
   */

  protected $casts = [
    'meta' => 'array',
    'schema' => 'array',
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'tenant_id'
  ];

  /**
   * Get course registrations
   *
   * @var array
   */
  public function grade()
  {
    return $this->belongsTo('App\CourseGrade','course_grade_id');
  }

  /**
   * Get course registrations
   *
   * @var array
   */
  public function instructor()
  {
    return $this->belongsTo('App\User','instructor_id','id');
  }

  /**
   * Get course subject
   *
   * @var array
   */
  public function subject()
  {
    return $this->belongsTo('App\Subject','subject_id','id');
  }

  /**
   * Get course registrations
   *
   * @var array
   */
  public function registrations()
  {
    return $this->hasMany('App\Registration');
  }

  /**
   *  Setup model event hooks
   */
  public static function boot()
  {
    parent::boot();
    self::creating(function ($model) {
      $model->uuid = (string) Uuid::generate(4);
      if (is_null($model->name)) {
        $model->name = $model->subject->name;
      }

      if (is_null($model->code)) {
        $model->code = $model->parse_course_code();
      }
      
      if (is_null($model->schema)) {
        $model->schema = config('edu.default_course_schema');
      }
    });
  }

  private function parse_course_code(){
    return strtoupper($this->subject->code.'-'.str_replace(' ','-',$this->grade->name));
  }
}
