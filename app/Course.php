<?php

namespace App;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Bouncer;

class Course extends Model
{
  // /**
  //  * The "booted" method of the model.
  //  *
  //  * @return void
  //  */
  // protected static function booted()
  // {
  //   static::addGlobalScope(new TenantScope);
  // }

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'name',
    'description',
    'meta',
    'tenant_id',
    'instructor_id',
    'subject_id',
    'code',
    'course_grade_id',
    'schema',
    'status_id',
    'start_date',
    'end_date',
  ];

  /**
   * The attributes that should be mutated to dates.
   *
   * @var array
   */
  protected $dates = [
    'end_date',
    'start_date',
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
   * Get course grade
   *
   * @var array
   */
  public function grade()
  {
    return $this->belongsTo('App\CourseGrade','course_grade_id');
  }

  /**
   * Get course instructor
   *
   * @var array
   */
  public function instructor()
  {
    return $this->belongsTo('App\Instructor','instructor_id','id');
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
   * Get course status
   *
   * @var array
   */
  public function status()
  {
    return $this->belongsTo('App\CourseStatus');
  }

  /**
   *  Setup model event hooks
   */
  public static function boot()
  {
    parent::boot();
    self::creating(function ($model) {
      if (is_null($model->name)) {
        $model->name = $model->subject->name;
      }

      if (is_null($model->code)) {
        $model->code = $model->parse_course_code();
      }
      
      if (is_null($model->schema)) {
        $model->schema = config('edu.default.course_schema');
      }
    });

    self::created(function ($model) {
      if (request()->has('instructor_id') && $model->wasChanged('instructor_id')) {
        $model->instructor->assignInstructor($model);
      };
    });

    self::saved(function ($model) {
      if (request()->has('instructor_id') && $model->wasChanged('instructor_id') && isset($model->instructor_id)) {
        $model->instructor->assignInstructor($model);
      }
    });

    self::deleting(function ($model) {
      if ($model->registrations()->count() > 0) {
        $model->registrations()->delete();
      }
    });
  }

  public function scopeOfCourseGrade($query, $course_grade_id)
  {
    return $query
      ->where('course_grade_id', $course_grade_id);
  }

  public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }

  public function scopeValidCourses($query, Student $student)
  {
    $course_ids = Registration::where('user_id', $student->id)->pluck('course_id');

    return $query
      ->ofCourseGrade($student->grade['id'])
      ->ofTenant($student->tenant_id)
      ->whereNotIn('id', $course_ids);
  }

  private function parse_course_code() {
    return strtoupper($this->subject->code.'-'.str_replace(' ','-',$this->grade->name));
  }
}