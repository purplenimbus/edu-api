<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use Illuminate\Support\Facades\Auth;
use App\Nimbus\NimbusEdu;
use App\Http\Requests\StoreStudent;
use App\Http\Requests\GetStudents;
use App\Http\Requests\StoreUser as StoreUser;

class StudentController extends Controller
{
  public function index(GetStudents $request) {
    $tenant = Auth::user()->tenant()->first();

    $nimbus_edu = new NimbusEdu($tenant);

    $query = [
      ['tenant_id', '=', $tenant->id]
    ];

    if($request->has('course_grade_id')){
      array_push($query, ['meta->course_grade_id', '=', (int)$request->course_grade_id]);
    }

    if($request->has('status')){
      array_push($query, [
        'account_status_id',
        '=',
        (int)$nimbus_edu->getStatusID($request->status)->id
      ]);
    }

		$students = Student::with(['status_type'])->where($query);

		if($request->has('paginate')) {
      $students = $students->paginate($request->paginate);
    }else{
      $students = $students->get();
    }

    return response()->json($students, 200);
	}

  public function create(StoreStudent $request) {
    $tenant = Auth::user()->tenant()->first();

    $nimbus_edu = new NimbusEdu($tenant);

    $student = $nimbus_edu->create_student($request->all());

    $student->load(['status_type:id,name']);

    return response()->json($student, 200);
  }
}
