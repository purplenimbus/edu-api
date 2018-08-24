<?php
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $count = 0;
		$records = factory(App\Tenant::class, 1)
			->create(['meta' => [ 'country' => 'nigeria']])
			->each(function($tenant)use($count){
				
				$students = factory(App\User::class,'student',7)
					->create([
						'tenant_id' => $tenant->id,
						'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					])
					->each(function($student)use($tenant,$count){
						echo "Student Created".$student->uuid."\r\n";
						//var_dump($student->uuid);
						$course = App\Course::all()->each(function($course)use($tenant,$student){
							
							
							$registration = App\Registration::create([
								'course_id' => $course->id,
								'tenant_id' => $tenant->id,
								'user_id' => $student->id,
								'meta' => [
										'grades' => factory(App\Scores::class)->make()
									]
							]);
							echo "Registered Student in ".$course->code."Registration id : ".$registration->uuid."\r\n";
							
						});
						
						$count++;
					});
				
				\Log::info('Created '.$students->count().' students');
				
				$demoStudent 	=	factory(App\User::class,'student',1)->create([
					'tenant_id' => 1,
					'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					'firstname'		=>	'demo',
					'lastname'		=>	'student',
					'email'		=>	'student@yopmail.com',
					'password'	=>	app('hash')->make('demo'),
					'access_level' => 1
				])->each(function($student)use($count,$tenant){ 
				
					/*$course = App\Course::all()->each(function($course)use($tenant,$student){
								
								$registration = App\Registration::create([
									'course_id' => $course->id,
									'tenant_id' => $tenant->id,
									'user_id' => $student->id,
									'meta' => [
										'grades' => factory(App\Scores::class)->make()
									]
								]);
								
								echo "Registered Student in ".$course->code."\r\n";
								
								var_dump($registration->uuid);
							});*/
							
					$count++; 
				
				});
			
				$demoTeacher 	=	factory(App\User::class,'teacher',1)->create([
						'tenant_id' => $tenant->id,
						'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
						'firstname'		=>	'demo',
						'lastname'		=>	'teacher',
						'email'		=>	'teacher@yopmail.com',
						'password'	=>	app('hash')->make('demo'),
						'access_level' => 1
					])->each(function($teacher)use($count){ $count++; });
				
				/*$teachers = factory(App\User::class,'teacher',2)
					->create([
						'tenant_id' => $tenant->id,
						'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					])
					->each(function($teacher)use($tenant,$count){
						echo "Teacher Created".$teacher->uuid."\r\n";
						$count++;
					});
					
				\Log::info('Created '.$teachers->count().' teachers');*/
				
				$admin 	=	factory(App\User::class,'admin',1)->create([
						'tenant_id' => $tenant->id,
						'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
						'firstname'		=>	'anthony',
						'lastname'		=>	'akpan',
						'email'		=>	'anthony.akpan@hotmail.com',
						'password'	=>	app('hash')->make('easier'),
						'access_level' => 3
					])->each(function($admin)use($count){ $count++; });
			
			});
			
		\Log::info('Created '.$count.' total test users');
    }
}
