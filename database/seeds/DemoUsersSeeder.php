<?php

use Illuminate\Database\Seeder;
use App\Nimbus\Institution;

class DemoUsersSeeder extends Seeder
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
			->create(['name'=> 'showers christian high hchool','meta' => [ 'country' => 'nigeria','current_term' => 'first']])
			->each(function($tenant)use($count){
				$institution = new Institution($tenant->id,$tenant->meta->country);				

				$admin 	=	factory(App\User::class,'admin',1)->create([
					'tenant_id' => $tenant->id,
					'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					'firstname'		=>	'ekama',
					'lastname'		=>	'akpan',
					'email'		=>	'info@showersedu.com',
					'password'	=>	app('hash')->make('info@showersedu.com'),
				])->each(function($admin)use($count){ $count++; });

				$superadmin 	=	factory(App\User::class,'superadmin',1)->create([
					'tenant_id' => $tenant->id,
					'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					'firstname'		=>	'anthony',
					'lastname'		=>	'akpan',
					'email'		=>	'anthony.akpan@hotmail.com',
					'password'	=>	app('hash')->make('easier'),
				])->each(function($superadmin)use($count){ $count++; });

				$demoTeacher 	=	factory(App\User::class,'teacher',1)->create([
					'tenant_id' => $tenant->id,
					'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					'firstname'		=>	'demo',
					'lastname'		=>	'teacher',
					'email'		=>	'teacher@yopmail.com',
					'password'	=>	app('hash')->make('demo'),
				])->each(function($teacher)use($count){ $count++; });

				$demoStudent 	=	factory(App\User::class,'student',1)->create([
							'tenant_id' => $tenant->id,
							'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
							'firstname'		=>	'demo',
							'lastname'		=>	'student',
							'email'		=>	'student@yopmail.com',
							'password'	=>	app('hash')->make('demo'),
							'meta' => ['course_grade_id' => 1]
						])->each(function($student)use($count){ $count++; });
		});

		\Log::info('Created '.$count.' total test users');
    }
}
