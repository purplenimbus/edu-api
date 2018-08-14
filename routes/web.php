<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

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
});
