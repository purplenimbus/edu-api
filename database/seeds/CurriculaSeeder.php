<?php

use App\NimbusEdu\Institution;
use Illuminate\Database\Seeder;

class CurriculaSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $institution = new Institution();

    $institution->generateCurriculum();
  }
}
