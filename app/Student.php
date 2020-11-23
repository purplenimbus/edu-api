<?php

namespace App;

use App\User;
use App\StatusType;
use App\CourseGrade;
use App\Registration;
use App\SchoolTerm;
use App\Scopes\TenantScope;
use App\UserGroup;
use App\UserGroupMember;
use Bouncer;
use Illuminate\Support\Arr;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Student extends User
{
  use HasRolesAndAbilities;

  public $table = "users";
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
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'grade'
  ];

  public function newQuery($excludeDeleted = true)
  {
    return parent::newQuery($excludeDeleted)
      ->whereIs('student');
  }

  public function generateStudentId() {
    return date("Y").sprintf("%04d", $this->id);
  }

    /**
   *  Get course grade type
  */
  public function getGuardianAttribute()
  {
    $member = UserGroupMember::whereHas('group', function($query) {
      $query->where('type_id', 1);
    })
    ->where('user_id', $this->id)
    ->first();

    return !is_null($member) ? $member->group->owner : $member;
  }

  /**
   *  Get course grade type
  */
  public function getGradeAttribute()
  {
    $meta = Arr::get($this, 'meta', null);

    if (is_null($meta) || !isset($meta->course_grade_id)) {
      return;
    } 

    return CourseGrade::where('id', $this->meta->course_grade_id)
      ->first()
      ->only('id','name','alias');
  }

  /**
   *  Get course grade type
  */
  public function getTranscripts() {
    if (is_null($this->id)) {
      return;
    }

    return SchoolTerm::with([
      'registrations.course',
      'registrations.score',
      'registrations' => function($query) {
        $query->whereUserId($this->id);
      }
    ])
    ->whereTenantId($this->tenant->id)
    ->get();
  }
  
  public function scopeOfCourseGrade($query, $course_grade_id)
  {
    return $query->where('meta->course_grade_id', $course_grade_id);
  }


  public function scopeOfUnregistered($query, $course_id)
  {
    $course = Course::find($course_id);
    $registrations = Registration::where('course_id', $course_id)->pluck('user_id');

    return $query
      ->where('meta->course_grade_id', $course->course_grade_id)
      ->ofTenant($course->tenant_id)
      ->whereNotIn('id', $registrations);
  }

  public function scopeOfTenant($query, $tenant_id)
  {
    return $query->where('tenant_id', $tenant_id);
  }

  public function registrations()
  {
    return $this->hasMany('App\Registration', 'user_id', 'id');
  }
}
