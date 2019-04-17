<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessBatch;
use App\Http\Requests\GetCourses as GetCourses;
use App\Http\Requests\GetSubjects as GetSubjects;
use App\Lesson as Lesson;
use App\Subject as Subject;
use App\Curriculum as Curriculum;

class CurriculumController extends Controller
{
	/**
     * Batch create subjects
     *
     * @return void
     */
	public function generateCurriculum(GenerateCurriculum $request){
		$tenant_id = Auth::user()->tenant()->first()->id;

		ProcessBatch::dispatch($tenant, $request->course_grade_id, $request->type);

		return response()->json(['message' => 'your request is being processed'],200);
	}
	/**
     * List all subjects
     *
     * @return void
     */
	public function subjects(GetSubjects $request){
		if($request->has('subject_id')){
			response()->json(Subject::find($request->subject_id), 200);

			return $subjects;
		}

		return response()->json(Subject::all(), 200);
	}

	/**
     * List all classes
     *
     * @return void
     */
	public function listClasses(){
		return response()->json(CourseGrade::all(), 200);
	}
	/**
   * List lessons
   *
   * @return void
   */
	public function lessons(Request $request)
	{
		$tenant_id = Auth::user()->tenant()->first()->id;

		$query = [];

		array_push($query,['tenant_id', '=', $tenant_id]);
		//TO DO: create GetLessons Request
		if(!$request->has('course_id')){
			$message = 'course id required';
			
			return response()->json($message,500);
		}else{
			array_push($query,['course_id', '=', $request->course_id]);
			array_push($query,['parent_id', '=', null]);
		}	

		if($request->has('instructor_id')){
			array_push($query,['meta->instructor_id', '=', $request->instructor_id]);
		}

		$lessons = $request->has('paginate') ? Lesson::with('sub_lessons','course')->where($query)->paginate($request->paginate) : Lesson::with('sub_lessons','course')->where($query)->get();

		if(sizeof($lessons)){
			return response()->json($lessons,200);
		}else{
			
			$message = 'no lessons found for course id : '.$request->course_id;
			
			return response()->json(['message' => $message],404);
		}
	}

  /**
   * Batch create subjects
   *
   * @return void
   */
  public function getCourseLoad($course_grade_id){
  	// Move to NimbusEdu ?
  	$curriculum = Curriculum::with('grade')->where('course_grade_id',$course_grade_id)->first();

  	$course_load = [];

  	if(isset($curriculum->course_load)){
  		foreach ($curriculum->course_load as $key => $section) {
				//var_dump($section);
  			$course_load[$key] = [];

  			if(sizeof($section)){
  				foreach ($section as $subject_id) {
  					if(is_int($subject_id)){
  						$subject = Subject::find($subject_id);

  						$course_load[$key][] = $subject->only(['name','code','id','group']);
  					}
  				}
  			}
  		}

  		$curriculum = $curriculum->toArray();

  		$curriculum['course_load'] = $course_load;
  	}

  	return response()->json($curriculum, 200);
  }
}
