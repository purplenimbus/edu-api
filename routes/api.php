<?php
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

use Illuminate\Support\Facades\Route;

Route::group([
  'middleware' => ['cors'],
  'prefix' => 'v'.env('API_VERSION',1),
], function() {
  Route::post('/login','Auth\LoginController@authenticate');
  Route::post('/logout','Auth\LoginController@logout');
  Route::get('/subjects', 'CurriculumController@subjects');
  Route::get('/grades/list', 'CurriculumController@listClasses');
  Route::get('/curriculum','CurriculumController@getCourseLoad')->middleware(['jwt.auth']);
  Route::get('/account_status','UserController@getAccountStatuses');
  Route::get('/course_statuses','CourseController@course_statuses');

  Route::post('/register','Auth\RegisterController@create');
  Route::get('/email/resend','Auth\VerificationController@resend')
		->name('verification.resend');
	Route::get('/email/verify/{id}','Auth\VerificationController@verify')
		->name('verification.verify');
	// Password Reset
  Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
  Route::post('/password/reset', 'Auth\ResetPasswordController@reset')->name('password.reset');
  Route::get('/password/reset', 'Auth\ResetPasswordController@getToken');

  Route::group([
    'middleware' => ['jwt.auth'],
    'prefix' => 'auth'
  ], function() {
    Route::get('/user', 'Auth\LoginController@currentUser');
  });

  Route::group([
    'middleware' => ['jwt.auth','verified']
  ], function() {
    /* Tenants */
    Route::group([
      'prefix' => '/tenants',
    ], function() {
      Route::post('/', 'TenantController@create');
      Route::group([
        'prefix' => '/{id}'
      ], function() {
        Route::get('/', 'TenantController@show');
        Route::put('/', 'TenantController@update');

        Route::get('/settings', 'TenantController@getSettings');
        Route::put('/settings', 'TenantController@updateSetting');

        Route::post('/term', 'TenantController@updateTerm');
        Route::group([
          'prefix' => '/bank_accounts'
        ], function() {
          Route::get('/', 'BankAccountController@index');
          Route::post('/', 'BankAccountController@create');
          Route::put('/{bank_account_id}', 'BankAccountController@update');
          Route::delete('/{bank_account_id}', 'BankAccountController@delete');
        });
      });
    });

    /* Invoices */
    Route::group([
      'prefix' => '/invoices',
      'middleware' => ['hasbankaccount']
    ], function() {
      Route::get('/', 'InvoiceController@index');
      Route::post('/', 'InvoiceController@create');
      Route::group([
        'prefix' => '/{invoice_id}'
      ], function() {
        Route::get('/', 'InvoiceController@show');
        Route::get('/verify', 'InvoiceController@verify');
        Route::put('/', 'InvoiceController@update');
        Route::delete('/', 'InvoiceController@delete');

        Route::group([
          'prefix' => '/line_items'
        ], function() {
          Route::get('/', 'LineItemsController@index');
          Route::delete('/', 'LineItemsController@delete');
        });
      });
    });

    Route::group([
      'prefix' => '/transactions'
    ], function() {
      Route::get('/verify', 'TransactionController@verify');
    });

    /* Transactions */

    /* Terms */
    Route::group([
      'prefix' => '/school_terms',
      'middleware' => ['checksubscription']
    ], function() {
      Route::get('', 'SchoolTermController@index');
      Route::post('', 'SchoolTermController@create');

      Route::group([
        'prefix' => '/{id}'
      ], function() {
        Route::get('', 'SchoolTermController@show');
        Route::put('', 'SchoolTermController@update');
      });
    });

    /* Courses */
    Route::group([
      'prefix' => '/courses',
    ], function() {
      Route::group([
        'middleware' => ['can:view-courses']
      ], function() {
        Route::get('', 'CourseController@index');
        Route::get('/not_registered','CourseController@not_registered');
      });

      Route::group([
        'middleware' => ['can:edit-courses']
      ], function() {
        Route::post('', 'CourseController@create');
        Route::post('/batch', 'CourseController@batch');
        Route::post('/generate', 'CourseController@generate');
        Route::delete('', 'CourseController@delete');
      });

      Route::group([
        'middleware' => ['can:view-courses'],
        'prefix' => '/{id}',
      ], function() {
        Route::put('/', 'CourseController@update');
        Route::get('/', 'CourseController@show');
      });
    });
    /* Lessons */
    Route::get('/lessons', 'CurriculumController@lessons')->middleware('checksubscription');
    /*  Curriculum */
    Route::post('/curriculum/generate', 'CurriculumController@generateCurriculum');

    /*  Registrations */
    Route::group([
      'prefix' => '/registrations',
      'middleware' => ['checksubscription'],
    ], function() {
      Route::get('/','RegistrationController@index')->middleware('can:view-registrations');
      Route::put('/scores','RegistrationController@update_scores');
      Route::delete('/','RegistrationController@delete');
      Route::post('/batch', 'RegistrationController@batch');
    });

    /* Users */
    Route::group([
      'prefix' => '/users',
      'middleware' => ['can:view-users'],
    ], function() {
      Route::get('/', 'UserController@index');
      Route::post('/', 'UserController@create');
      Route::post('/batch', 'UserController@batchUpdate');
      
      Route::group([
        'prefix' => '/{id}'
      ], function() {
        Route::get('/', 'UserController@show');
        Route::put('/', 'UserController@update');
      });
    });

    /* Instructors */
    Route::group([
      'prefix' => '/instructors',
      'middleware' => ['checksubscription'],
    ], function() {
      Route::get('/', 'InstructorController@index')->middleware('can:view-instructors');
      Route::post('/', 'InstructorController@create');

      Route::group([
        'prefix' => '/{id}'
      ], function() {
        Route::post('/assign', 'InstructorController@assignInstructor');
        Route::put('/', 'InstructorController@edit');
        Route::get('/', 'InstructorController@show');
      });
    });

    /* Students */
    Route::group([
      'prefix' => '/students',
      'middleware' => ['checksubscription'],
    ], function() {
      Route::get('/', 'StudentController@index')->middleware('can:view-students');
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

    /* Guardians */
    Route::group([
      'prefix' => '/guardians',
      'middleware' => ['checksubscription'],
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
        });
      });
    });

    /* Payment Profiles */
    Route::group([
      'prefix' => '/payment_profiles',
      'middleware' => [],
    ], function() {
      Route::get('/', 'PaymentProfilesController@index');
      Route::post('/', 'PaymentProfilesController@create');

      Route::group([
        'prefix' => '/{id}'
      ], function() {
        Route::put('/', 'PaymentProfilesController@update');
        Route::delete('/', 'PaymentProfilesController@delete');
      });
    });
  });
});