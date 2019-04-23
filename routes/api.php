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
	Route::get('/subjects', 'CurriculumController@subjects');
	Route::get('/grades/list', 'CurriculumController@listClasses');
	Route::get('/curriculum/{course_grade_id}','CurriculumController@getCourseLoad');

	Route::group([
		'middleware' => ['jwt.auth']
	], function() {
		/* Tenants */
		Route::get('/settings', 'TenantController@getSettings');
		/* Courses */
		Route::get('/courses', 'CourseController@getCourses');
		Route::post('/courses/new', 'CourseController@createCourse');
		Route::post('/courses/edit', 'CourseController@updateCourse');
		Route::post('/courses/batch', 'CourseController@batchUpdate');
		Route::post('/courses/generate', 'CourseController@generateCourses');
		//Route::post('/courses/list', 'CourseController@courseStudentList');
		/* Lessons */
		Route::get('/lessons', 'CurriculumController@lessons');
		/*  Curriculum */
		Route::post('/curriculum/generate', 'CurriculumController@generateCurriculum');
		/*  Registrations */
		Route::get('/registrations','RegistrationController@registrations');
		Route::post('/register','RegistrationController@registerStudents');
		/* Users */
		Route::get('/users/index', 'UserController@userList');
		Route::get('/user', 'UserController@getUser');
		Route::post('/users/batch', 'UserController@batchUpdate');
		Route::post('/user', 'UserController@saveUser');
		/* Instructors */
		Route::post('/instructors', 'InstructorController@assignInstructor');
	});
});