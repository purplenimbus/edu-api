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
    'grade',
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
}
