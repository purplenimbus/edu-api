<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Course as Course;

use App\Http\Requests\StoreCourse as StoreCourse;
use App\Http\Requests\StoreBatch as StoreBatch;

use App\Jobs\ProcessBatch;
use App\Jobs\GenerateCourses;


class CourseController extends Controller
{
    //var	$client;
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		//$this->middleware('jwt.auth');
    }
	
	/**
     * List courses
     *
     * @return void
     */
    public function getCourses($tenant_id,Request $request)
    {
        $query = [
					['tenant_id', '=', $tenant_id]
				];
		
		if($request->has('course_id')){
			array_push($query,['course_id', '=', $request->course_id]);
		}	
				
		$courses = $request->has('paginate') ? 
						Course::with('registrations')
								->where($query)
								->paginate($request->paginate) 
								
					: Course::with('registrations')
							->where($query)
							->get();
						
		if(sizeof($courses)){
			return response()->json($courses,200);
 		}else{
			
			$message = 'no courses found for tenant id : '.$tenant_id;
			
			return response()->json(['message' => $message],204);
		}
		
    }
	

	/**
     * Create a new course
     *
     * @return void
     */
	public function createCourse($tenant_id,StoreCourse $request){
		dd($request->all());
		
		//$data = $request->all();
		
		/*$data['tenant_id'] = $tenant_id;

		$data['code'] = $data['class']['name']
		
		$course = Course::create($data);
		
		return response()->json($course,200)->setCallback($request->input('callback'));*/
	}

	/**
     * Batch create subjects
     *
     * @return void
     */
	public function batchUpdate($tenant_id,StoreBatch $request){
		ProcessBatch::dispatch($tenant_id,$request->all()[0],$request->type);

		return response()->json(['message' => 'your request is being processed'],200);
	}

	/**
     * Generate courses based on subjects
     *
     * @return void
     */
	public function generateCourses($tenant_id,Request $request){
		GenerateCourses::dispatch($tenant_id,Subject::all());

		return response()->json(['message' => 'your request is being processed'],200);
	}
}
