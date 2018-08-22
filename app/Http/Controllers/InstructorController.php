<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course as Course;
use App\Http\Requests\AssignInstructor as AssignInstructor;

class InstructorController extends Controller
{
	/**
     * Assign Instructor to a Course
     *
     * @return void
     */
	public function assignInstructor($tenant_id,AssignInstructor $request){
		$course = Course::find($request->course_id);

		$course->fill($request->only('instructor_id'));

		$course->save();

		return response()->json($course,200);
	}
}
