<?php
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group([
	'middleware' => ['cors'],
	'prefix' => 'v'.env('API_VERSION',1),
], function() {
	Route::post('/login','Auth\LoginController@authenticate');
	Route::post('/logout','Auth\LoginController@logout');
	Route::get('/subjects', 'CurriculumController@subjects');
	Route::get('/grades/list', 'CurriculumController@listClasses');
	Route::get('/curriculum/{course_grade_id}','CurriculumController@getCourseLoad');
	Route::get('/account_status/index','UserController@getAccountStatuses');

	Route::group([
		'middleware' => ['jwt.auth']
	], function() {
		/* Tenants */
		Route::get('/settings', 'TenantController@settings');
		Route::put('/tenants', 'TenantController@update');
		/* Courses */
		Route::get('/courses', 'CourseController@index');
		Route::post('/courses', 'CourseController@create');
		Route::put('/courses', 'CourseController@update');
		Route::post('/courses/batch', 'CourseController@batch');
		Route::post('/courses/generate', 'CourseController@generate');
		Route::get('/courses/not_registered','CourseController@not_registered');
		//Route::post('/courses/list', 'CourseController@courseStudentList');
		/* Lessons */
		Route::get('/lessons', 'CurriculumController@lessons');
		/*  Curriculum */
		Route::post('/curriculum/generate', 'CurriculumController@generateCurriculum');
		/*  Registrations */
		Route::get('/registrations','RegistrationController@registrations');
		Route::post('/register','RegistrationController@registerStudents');
		/* Users */
		Route::get('/users', 'UserController@index');
		Route::get('/user', 'UserController@getUser');
		Route::post('/users/batch', 'UserController@batchUpdate');
		Route::post('/user', 'UserController@saveUser');
		/* Instructors */
		Route::get('/instructors', 'InstructorController@index');
		Route::post('/instructors', 'InstructorController@create');
		Route::post('/instructors/assign', 'InstructorController@assignInstructor');
		/* Students */
		Route::get('/students', 'StudentController@index');
		Route::post('/students', 'StudentController@create');
		Route::put('/students', 'StudentController@edit');
	});
});