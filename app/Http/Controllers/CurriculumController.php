<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcessBatch;
use App\Http\Requests\GetCourses;
use App\Http\Requests\GetCourseGrade;
use App\Http\Requests\GetSubjects;
use App\Lesson;
use App\Subject;
use App\Curriculum;
use App\CurriculumCourseLoad;
use App\CourseGrade;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Nimbus\Syllabus;

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
    return response()->json(CourseGrade::get(['alias','description','id','name']), 200);
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
  public function getCourseLoad(GetCourseGrade $request, $course_grade_id){
    $tenant = Auth::user()->tenant()->first();
    $nimbus_syllabus = new Syllabus($tenant);
    $course_load = QueryBuilder::for(CurriculumCourseLoad::class)
      ->allowedFields(
        'curriculum.id',
        'curriculum.grade',
        'subject',
        'type',
        'curriculum.has_students'
      )
      ->allowedIncludes(
        'curriculum.grade',
        'subject',
        'type',
        'curriculum.has_students'
      )
      ->allowedFilters([
        AllowedFilter::callback('type_id', function (Builder $query, $value) {
            $query->where('type_id', $value);
        }),
        AllowedFilter::callback('type', function (Builder $query, $value) use ($nimbus_syllabus) {
            $query->where(
              'type_id',
              (int)$nimbus_syllabus
                ->getCurriculumCourseLoadType($value)
                ->id
            );
        })
      ])
      ->whereHas('curriculum', function($query) use ($course_grade_id){
        $query->where('course_grade_id', $course_grade_id);
      })
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($course_load, 200);
  }
}
