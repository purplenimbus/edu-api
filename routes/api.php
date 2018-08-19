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

Route::post('/v'.env('API_VERSION',1).'/login','Auth\LoginController@authenticate');

/* Subjects */
Route::get('/v'.env('API_VERSION',1).'/subjects', 'CourseController@subjects'); //List all Subjects
	
Route::prefix('v'.env('API_VERSION',1).'/{tenant}')->group(function () {
	
	/* Courses */
	Route::get('/courses', 'CourseController@courses'); //List all courses for a certain tenant
	
	Route::post('/courses', 'CourseController@createCourse'); //create new course

	/* lessons */
	Route::get('/lessons', 'CourseController@lessons'); //List all registrations for a certain tenant

	/* Registrations */
	Route::get('/registrations','CourseController@registrations'); //List all registrations for a certain tenant

	Route::post('/coureStudentList', 'CourseController@coureStudentList'); //Add registrations for a user in a certain tenant
	
	Route::get('/users', 'UserController@index'); //List all users for a certain tenant
	Route::get('/users/{user_id}', 'UserController@get'); //List all details for a certain user
	Route::post('/users/{user_id}', 'UserController@save'); //Update user for a certain tenant
	Route::post('/users/batch', 'UserController@batch'); //Update user for a certain tenant
});