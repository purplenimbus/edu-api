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
			->create(['name'=> 'showers christian high school','meta' => [ 'country' => 'nigeria','current_term' => 'first']])
			->each(function($tenant)use($count){
				$institution = new Institution($tenant, $tenant->meta->country);				

				$admin 	=	factory(App\User::class,'admin',1)->create([
					'tenant_id' => $tenant->id,
					'firstname'		=>	'ekama',
					'lastname'		=>	'akpan',
					'email'		=>	'info@showersedu.com',
					'password'	=>	app('hash')->make('info@showersedu.com'),
				])->each(function($admin)use($count){ $count++; });

				$superadmin 	=	factory(App\User::class,'superadmin',1)->create([
					'tenant_id' => $tenant->id,
					'firstname'		=>	'anthony',
					'lastname'		=>	'akpan',
					'email'		=>	'anthony.akpan@hotmail.com',
					'password'	=>	app('hash')->make('easier'),
				])->each(function($superadmin)use($count){ $count++; });
		});

		\Log::info('Created '.$count.' total test users');
    }
}
