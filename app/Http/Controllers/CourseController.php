<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Course as Course;
use App\Lesson as Lesson;
use App\Registration as Registration;
use App\Tenant as Tenant;
use App\Subject as Subject;
//use GuzzleHttp\Client as Guzzle;


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
        /*$this->client	= 	new Guzzle([
								'defaults' => [
									'headers'  => ['content-type' => 'application/json', 'Accept' => 'application/json'],
								]
							]);*/
    }
	
	/**
     * List courses
     *
     * @return void
     */
    public function courses($tenant_id,Request $request)
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
			return response()->json($courses,200)->setCallback($request->input('callback'));
 		}else{
			
			$message = 'no courses found for tenant id : '.$tenant_id;
			
			return response()->json(['message' => $message],204)->setCallback($request->input('callback'));
		}
		
    }
	
	/**
     * List registrations
     *
     * @return void
     */
    public function registrations($tenant_id,Request $request)
    {
        $query = [
					['tenant_id', '=', $tenant_id]
				];
				
		$relationships = ['course'];
		
		if($request->has('user_id')){
			array_push($query,['user_id', '=', $request->user_id]);
			//array_push($relationships,'user');
		}

		if($request->has('course_id')){
			array_push($query,['course_id', '=', $request->course_id]);
			//array_push($relationships,'course');
		}			
				
		$registrations = $request->has('paginate') ? Registration::with($relationships)->where($query)->paginate($request->paginate) : Registration::with($relationships)->where($query)->get();
		
		/*if(sizeof($registrations)){
			//To Do Move to MiddleWare
			if($request->has('user_list')){
				//post to graph api
				//var_dump($registrations->toArray());
				
				$response = $this->client->request('PUT', 'http://graph.nimbus.com:8000/v1/'.$tenant_id.'/userList',['json' =>  $registrations->toArray()]);
				
				var_dump($response->getBody()->getContents());
				//return response()->json(json_decode($response->getBody()->getContents(),true),200);
			}else{
				return response()->json($registrations,200)->setCallback($request->input('callback'));
			}
		
		}else{
			
			$message = 'no registrations found for tenant id : '.$tenant_id;
			
			return response()->json(['message' => $message],204)->setCallback($request->input('callback'));
		}*/
    }
	
	/**
     * List lessons
     *
     * @return void
     */
    public function lessons($tenant_id,Request $request)
    {
        $query = [];
				
		if(!$tenant_id){
			$message = 'tenant id required';
			
			return response()->json($message,500)->setCallback($request->input('callback'));;
		}else{
			array_push($query,['tenant_id', '=', $tenant_id]);
		}
		
		if(!$request->has('course_id')){
			$message = 'course id required';
			
			return response()->json($message,500)->setCallback($request->input('callback'));;
		}else{
			array_push($query,['course_id', '=', $request->course_id]);
			array_push($query,['parent_id', '=', null]);
		}	

		if($request->has('instructor_id')){
			array_push($query,['meta->instructor_id', '=', $request->instructor_id]);
		}
				
		$lessons = $request->has('paginate') ? Lesson::with('sub_lessons','course')->where($query)->paginate($request->paginate) : Lesson::with('sub_lessons','course')->where($query)->get();
						
		if(sizeof($lessons)){
			return response()->json($lessons,200)->setCallback($request->input('callback'));;
		}else{
			
			$message = 'no lessons found for course id : '.$request->course_id;
			
			return response()->json(['message' => $message],404)->setCallback($request->input('callback'));;
		}
    }
	
	public function getTenant($tenant){
		try{
			$tenant = Tenant::where('username', $tenant)->first();
			
			return $tenant;
			
		}catch(Exception $e){
			return false;
		}
	}
	
	/**
     * List all subjects
     *
     * @return void
     */
	public function subjects(){
		return Subject::all();
	}
	/**
     * Create a new course
     *
     * @return void
     */
	public function createCourse($tenant_id,Request $request){
		dd($request->all());
		
		$this->validate($request, [
			'name' => 'required',
		]);
		
		$data = $request->all();
		
		$data['tenant_id'] = $tenant_id;
		
		$course = Course::create($data);
		
		return response()->json($course,200)->setCallback($request->input('callback'));
	}
}
