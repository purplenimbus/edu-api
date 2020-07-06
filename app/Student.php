<?php

namespace App;

use App\User;
use App\StatusType;
use App\CourseGrade;
use App\Registration;
use App\SchoolTerm;
use App\UserGroup;
use App\UserGroupMember;
use Bouncer;

class Student extends User
{
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
    $member = UserGroupMember::whereHas('group', function($query) { $query->where('type_id', 1);
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
    if (is_null($this->meta) && is_null($this->meta->course_grade_id)) {
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
    ])->get();
  }

  /**
   *  Setup model event hooks
  */
  public static function boot()
  {
    parent::boot();
    self::creating(function ($model) {
      $model->password = $model->createDefaultPassword();

      $status_type = StatusType::where('name', 'unenrolled')->first();

      if (!is_null($status_type)) {
        $model->account_status_id = $status_type->id;
      }
    });

    self::created(function ($model) {
      $model->assign('student');

      if (is_null($model->ref_id)) {
        $model->ref_id = $model->generateStudentId();

        $model->save();
      }
    });
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
      ->whereNotIn('id', $registrations);
  }
}
