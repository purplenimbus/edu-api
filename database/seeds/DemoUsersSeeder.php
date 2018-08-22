<?php

use Illuminate\Database\Seeder;

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
			->create([
				'meta'=>  [
					'settings' => [
						'classes' => [
							['id' => 1, 'name' => 'js 1','sections'=>['a','b'],'alias' => ''],
							['id' => 2, 'name' => 'js 2','sections'=>['a','b'],'alias' => ''],
							['id' => 3, 'name' => 'js 3','sections'=>['a','b'],'alias' => ''],
							['id' => 4, 'name' => 'ss 1','sections'=>['a','b'],'alias' => ''],
							['id' => 5, 'name' => 'ss 2','sections'=>['a','b'],'alias' => ''],
							['id' => 6, 'name' => 'ss 3','sections'=>['a','b'],'alias' => ''],
							['id' => 7, 'name' => 'a level','sections'=>['a','b'],'alias' => '']
						]
					]
				]
			])
			->each(function($tenant)use($count){
				$admin 	=	factory(App\User::class,'admin',1)->create([
					'tenant_id' => $tenant->id,
					'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					'firstname'		=>	'anthony',
					'lastname'		=>	'akpan',
					'email'		=>	'anthony.akpan@hotmail.com',
					'password'	=>	app('hash')->make('easier'),
					'access_level' => 3
				])->each(function($admin)use($count){ $count++; });

				$demoTeacher 	=	factory(App\User::class,'teacher',1)->create([
					'tenant_id' => $tenant->id,
					'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					'firstname'		=>	'demo',
					'lastname'		=>	'teacher',
					'email'		=>	'teacher@yopmail.com',
					'password'	=>	app('hash')->make('demo'),
					'access_level' => 1
				])->each(function($teacher)use($count){ $count++; });

				$demoStudent 	=	factory(App\User::class,'student',1)->create([
							'tenant_id' => $tenant->id,
							'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
							'firstname'		=>	'demo',
							'lastname'		=>	'student',
							'email'		=>	'student@yopmail.com',
							'password'	=>	app('hash')->make('demo'),
							'access_level' => 1
						])->each(function($teacher)use($count){ $count++; });
		});

		\Log::info('Created '.$count.' total test users');
    }
}
