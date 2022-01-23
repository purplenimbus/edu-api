<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {

  }
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    $this->call('OtherSeeders');
    $this->call('PermissionsSeeder');
    $this->call('SubjectsSeeder');
    if (App::environment(['local', 'staging'])) {
      $this->call('DemoUsersSeeder');
    }
  }
}
