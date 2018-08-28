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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/v'.env('API_VERSION',1).'/login','Auth\LoginController@authenticate')->middleware('cors');

/* Curriculum */
Route::get('/v'.env('API_VERSION',1).'/subjects', 'CurriculumController@subjects'); //List all Subjects
Route::get('/v'.env('API_VERSION',1).'/grades/list', 'CurriculumController@listClasses'); //List all Classe
Route::get('/v'.env('API_VERSION',1).'/curriculum/{course_grade_id}','CurriculumController@getCourseLoad'); //List all registrations for a certain tenants
	
Route::prefix('v'.env('API_VERSION',1).'/{tenant}')->group(function () {
	
	/* Tenants */
	Route::get('/settings', 'TenantController@getSettings');//Update user for a certain tenant

	/* Courses , Registrations & Lessons */
	Route::get('/courses', 'CourseController@getCourses'); //List all courses for a certain tenant
	Route::get('/lessons', 'CurriculumController@lessons'); //List all registrations for a certain tenant
	Route::post('/courses/new', 'CourseController@createCourse'); //create new course
	Route::post('/courses/edit', 'CourseController@updateCourse'); //create new course
	Route::post('/courses/batch', 'CourseController@batchUpdate');//Batch import subjects and courses
	//Route::post('/courses/list', 'CourseController@courseStudentList'); //Get students enrolled in a course
	Route::post('/courses/generate', 'CourseController@generateCourses'); //Generate new courses for a tenant

	/*  Curriculum */
	Route::post('/curriculum/generate', 'CurriculumController@generateCurriculum'); //Generate new courses for a tenant
	
	/*  Registrations */
	Route::get('/registrations','RegistrationController@registrations'); //List all registrations for a certain tenant

	Route::post('/register','RegistrationController@registerStudents'); //List all registrations for a certain tenant

	/* Users */
	Route::get('/users', 'UserController@userList'); //List all users for a certain tenant
	Route::get('/users/{user_id}', 'UserController@getUser'); //List all details for a certain user
	Route::post('/users/batch', 'UserController@batchUpdate');//Batch import or update users
	Route::post('/users/{user_id}', 'UserController@saveUser');//Update user for a certain tenant

	/* Instructors */
	Route::post('/instructors', 'InstructorController@assignInstructor'); //Assign instructor to a course

});