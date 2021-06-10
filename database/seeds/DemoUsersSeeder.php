<?php

use Illuminate\Database\Seeder;
use App\NimbusEdu\Institution;

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
        'country' => config('edu.default.country'),
        'name'=> 'showers christian high school',
      ])
      ->each(function($tenant)use($count){
          factory(App\User::class, 1)->create([
            'tenant_id' => $tenant->id,
            'firstname' => 'ekama',
            'lastname' => 'akpan',
            'email' => 'admin@yopmail.com',
            'password' => 'admin@yopmail.com',
          ])->each(function($admin)use($count){ 
            $count++; 
            $admin->assign('admin');
            $admin->markEmailAsVerified();
          });

          factory(App\User::class, 1)->create([
            'tenant_id' => $tenant->id,
            'firstname' => 'anthony',
            'lastname'  => 'akpan',
            'email' => 'superadmin@yopmail.com',
            'password' => 'superadmin@yopmail.com',
          ])->each(function($superadmin)use($count){ 
            $count++;
            $superadmin->assign('superadmin');
            $superadmin->markEmailAsVerified();
          });
      });

    \Log::info('Created '.$count.' total test users');
  }
}
