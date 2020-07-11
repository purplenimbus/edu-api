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
  Route::get('/account_status','UserController@getAccountStatuses');
  Route::get('/course_status','CourseController@course_statuses');

  Route::post('/register','Auth\RegisterController@create');
  Route::get('/email/resend','Auth\VerificationController@resend')
		->name('verification.resend');
	Route::get('/email/verify/{id}','Auth\VerificationController@verify')
		->name('verification.verify');
	// Password Reset
  Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
  Route::post('/password/reset', 'Auth\ResetPasswordController@reset');
  Route::get('/password/reset/{token}', 'Auth\ResetPasswordController@getToken');

  Route::group([
    'middleware' => ['jwt.auth','verified']
  ], function() {
    /* Tenants */
    Route::get('/settings', 'TenantController@settings');
    Route::put('/tenants', 'TenantController@update');
    Route::post('/tenants/term', 'TenantController@updateTerm');

    /* Terms */
    Route::group([
      'prefix' => '/terms'
    ], function() {
      Route::get('', 'TermController@index');
      Route::get('/show', 'TermController@show');
      Route::put('', 'TermController@update');
    });

    /* Courses */
    Route::group([
      'prefix' => '/courses'
    ], function() {
      Route::get('', 'CourseController@index');
      Route::post('', 'CourseController@create');
      Route::put('', 'CourseController@update');
      Route::post('/batch', 'CourseController@batch');
      Route::post('/generate', 'CourseController@generate');
      Route::get('/not_registered','CourseController@not_registered');
    });
    /* Lessons */
    Route::get('/lessons', 'CurriculumController@lessons');
    /*  Curriculum */
    Route::post('/curriculum/generate', 'CurriculumController@generateCurriculum');

    /*  Registrations */
    Route::group([
      'prefix' => '/registrations'
    ], function() {
      Route::get('/','RegistrationController@index');
      Route::put('/scores','RegistrationController@update_scores');
      Route::delete('/','RegistrationController@delete');
      Route::post('/batch', 'RegistrationController@batch');
    });

    /* Users */
    Route::get('/users', 'UserController@index');
    Route::get('/user', 'UserController@getUser');
    Route::post('/users/batch', 'UserController@batchUpdate');
    Route::post('/user', 'UserController@saveUser');

    /* Instructors */
    Route::group([
      'prefix' => '/instructors'
    ], function() {
      Route::get('/', 'InstructorController@index');
      Route::post('/', 'InstructorController@create');
      Route::post('/assign', 'InstructorController@assignInstructor');
      Route::put('/', 'InstructorController@edit');
    });

    /* Students */
    Route::group([
      'prefix' => '/students'
    ], function() {
      Route::get('/', 'StudentController@index');
      Route::post('/', 'StudentController@create');

      Route::group([
        'prefix' => '/{id}'
      ], function() {
        Route::get('/', 'StudentController@show');
        Route::put('/', 'StudentController@edit');
        Route::get('/transcripts', 'StudentController@transcripts');
        Route::get('/valid_courses', 'StudentController@valid_courses');
      });
    });

    Route::group([
      'prefix' => '/guardians'
    ], function() {
      Route::get('/', 'GuardianController@index');
      Route::post('/', 'GuardianController@create');

      Route::group([
        'prefix' => '/{id}'
      ], function() {
        Route::get('/', 'GuardianController@show');
        Route::put('/', 'GuardianController@update');
        Route::delete('/', 'GuardianController@destroy');

        Route::group([
          'prefix' => '/wards'
        ], function() {
          Route::get('/', 'WardController@index');
          Route::put('/', 'WardController@update');
          Route::delete('/', 'WardController@destroy');
        });
      });
    });
  });
});