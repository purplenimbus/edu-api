<?php

use App\Nimbus\Institution;
use Illuminate\Database\Seeder;

class SubjectsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Institution::generateSubjects();
  }
}
