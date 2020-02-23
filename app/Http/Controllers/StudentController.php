<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
  public function index(Request $request) {
  	$tenant_id = Auth::user()->tenant()->first()->id;

    $query = [
      ['tenant_id', '=', $tenant_id]
    ];

    if($request->has('course_grade_id')){
      array_push($query, ['meta->course_grade_id', '=', (int)$request->course_grade_id]);
    }

		$students = Student::with(['account_status:name,id'])->where($query);

		if($request->has('paginate')) {
      $students = $students->paginate($request->paginate);
    }else{
      $students = $students->get();
    }

    return response()->json($students, 200);
	}
}
