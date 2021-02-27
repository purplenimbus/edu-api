<?php

namespace App;

use App\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Bouncer;
use Illuminate\Support\Arr;

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
    'term_id',
    'instructor_id',
    'subject_id',
    'code',
    'student_grade_id',
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
   * Get tenant
   *
   * @var array
   */
  public function tenant()
  {
    return $this->belongsTo('App\Tenant');
  }

  /**
   * Get term
   *
   * @var array
   */
  public function term()
  {
    return $this->belongsTo('App\SchoolTerm');
  }
  /**
   * Get course grade
   *
   * @var array
   */
  public function grade()
  {
    return $this->belongsTo('App\StudentGrade', 'student_grade_id');
  }

  /**
   * Get course instructor
   *
   * @var array
   */
  public function instructor()
  {
    return $this->belongsTo('App\Instructor');
  }

  /**
   * Get course subject
   *
   * @var array
   */
  public function subject()
  {
    return $this->belongsTo('App\Subject');
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

  public function scopeOfStudentGrade($query, $student_grade_id)
  {
    return $query
      ->where('student_grade_id', $student_grade_id);
  }

  public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }

  public function scopeValidCourses($query, Student $student)
  {
    $course_ids = Registration::where('user_id', $student->id)->pluck('course_id');

    return $query
      ->ofStudentGrade($student->grade['id'])
      ->ofTenant($student->tenant_id)
      ->whereNotIn('id', $course_ids);
  }

  public function parse_course_code() {
    $subjectCode = Arr::get($this, "subject.code", "");
    $StudentGrade = Arr::get($this, "grade.name", "");
  
    return strtoupper($subjectCode.'-'.str_replace(' ', '-', $StudentGrade));
  }
}