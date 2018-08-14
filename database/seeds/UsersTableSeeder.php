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
			->create()
			->each(function($tenant)use($count){
				
				$students = factory(App\User::class,'student',7)
					->create([
						'tenant_id' => $tenant->id,
						'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					])
					->each(function($student)use($tenant,$count){
						var_dump($student->uuid);
						$count++;
					});
				
				\Log::info('Created '.$students->count().' students');
				
				$teachers = factory(App\User::class,'teacher',2)
					->create([
						'tenant_id' => $tenant->id,
						'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					])
					->each(function($teacher)use($tenant,$count){
						var_dump($teacher->uuid);
						$count++;
					});
					
				\Log::info('Created '.$teachers->count().' teachers');
			});
			
		$admin 	=	factory(App\User::class,'admin',1)->create([
				'tenant_id' => 1,
				'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
				'firstname'		=>	'anthony',
				'lastname'		=>	'akpan',
				'email'		=>	'anthony.akpan@hotmail.com',
				'password'	=>	app('hash')->make('easier'),
				'access_level' => 3
			])->each(function($user)use($count){
				$count++;
				/*factory(App\Activity::class,5)
					->create([ 
						'user_id' => $user->id,
						'tenant_id' => $user->tenant_id,
					]);*/
				
			});
			
		\Log::info('Created '.$count.' total test users');
    }
}
