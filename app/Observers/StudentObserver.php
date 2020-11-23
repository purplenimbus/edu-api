<?php

namespace App\Observers;

use App\Student;
use App\User;
use App\StatusType;

class StudentObserver
{
	/**
	 * Handle the student "created" event.
	 *
	 * @param  \App\Student  $student
	 * @return void
	 */
	public function created(Student $student)
	{
		$user = User::find($student->id);
		$user->assign('student');//Assign user model a role to return roles and permissions for JWT Claims
		$student->assign('student');

		if (is_null($student->ref_id)) {
			$student->ref_id = $student->generateStudentId();

			$student->save();
		}
	}

	/**
	 * Handle the student "updated" event.
	 *
	 * @param  \App\Student  $student
	 * @return void
	 */
	public function updated(Student $student)
	{
			//
	}

	/**
	 * Handle the student "deleted" event.
	 *
	 * @param  \App\Student  $student
	 * @return void
	 */
	public function deleted(Student $student)
	{
			//
	}

	/**
	 * Handle the student "restored" event.
	 *
	 * @param  \App\Student  $student
	 * @return void
	 */
	public function restored(Student $student)
	{
			//
	}

	/**
	 * Handle the student "force deleted" event.
	 *
	 * @param  \App\Student  $student
	 * @return void
	 */
	public function forceDeleted(Student $student)
	{
			//
	}

	/**
	 * Handle the student "creating" event.
	 *
	 * @param  \App\Student  $student
	 * @return void
	 */
	public function creating(Student $student){
		$student->password = $student->createDefaultPassword();

		$status_type = StatusType::where('name', 'unenrolled')->first();

		if (!is_null($status_type)) {
			$student->account_status_id = $status_type->id;
		}
	}
}
