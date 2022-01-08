<?php

namespace App;

use App\User;
use App\StudentGrade;
use App\Registration;
use App\SchoolTerm;
use App\UserGroupMember;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Student extends User
{
  use HasRolesAndAbilities;

  public $table = "users";

  const StatusTypes = [
    'created' => 1,
    'unenrolled' => 2,
    'registered' => 3,
    'archived' => 6,
  ];

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
    return Carbon::now()->get('year').sprintf("%04d", $this->id);
  }

  /**
   *  Get student grade type
  */
  public function getGuardianAttribute()
  {
    $member = UserGroupMember::whereHas('group', function($query) {
      $query->where('type_id', UserGroup::Types['wards']);
    })
    ->where('user_id', $this->id)
    ->first();

    return !is_null($member) ? $member->group->owner : $member;
  }

  /**
   *  Get student id
  */
  public function getStudentIdAttribute()
  {
    return $this->ref_id;
  }

  /**
   *  Get course grade type
  */
  public function getGradeAttribute()
  {
    $meta = Arr::get($this, 'meta', null);

    if (is_null($meta) || !isset($meta->student_grade_id)) {
      return;
    } 

    return StudentGrade::where('id', $this->meta->student_grade_id)
      ->first()
      ->only('id','name','alias');
  }

  /**
   *  Get student transcripts
  */
  public function getTranscriptsAttribute() {
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

  public function scopeOfStudentGrade($query, $student_grade_id)
  {
    return $query->where('meta->student_grade_id', $student_grade_id);
  }


  public function scopeOfUnregistered($query, $course_id)
  {
    $course = Course::find($course_id);
    $registrations = Registration::where('course_id', $course_id)->pluck('user_id');

    return $query
      ->ofStudentGrade($course->student_grade_id)
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
