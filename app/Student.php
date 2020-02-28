<?php

namespace App;

use App\User;
use App\StatusType;
use App\CourseGrade;

class Student extends User
{
  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'grade', 'type'
  ];

  public function newQuery($excludeDeleted = true)
	{
	  return parent::newQuery($excludeDeleted)
	  	->role('student');
	}

	public function generateStudentId() {
    return date("Y").sprintf("%04d", $this->id);
  }

  /**
   *  Get course grade type
  */
  public function getGradeAttribute()
  {
    if (is_null($this->meta->course_grade_id)) {
    	return;
    } 

    return CourseGrade::where('id', $this->meta->course_grade_id)
    	->first()
    	->only('id','name','alias');
  }

  /**
	 *  Setup model event hooks
  */
	public static function boot()
	{
		parent::boot();
		self::creating(function ($model) {
			$model->password = $model->createDefaultPassword();
			$model->assignRole('student');
			$status_type = StatusType::where('name', 'unenrolled')->first();

			if (!is_null($status_type)) {
				$model->account_status_id = $status_type->id;
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

    return $query
      ->leftJoin('registrations', 'users.id' , '=', 'registrations.user_id')
      ->leftJoin('courses', 'courses.id' , '=', 'registrations.course_id')
      ->where('users.meta->course_grade_id', $course->course_grade_id)
      ->where('users.tenant_id', $course->tenant_id)
      ->whereNull('registrations.id')
      ->select('users.*');
  }
}
