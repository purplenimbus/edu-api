<?php
use Illuminate\Database\Seeder;
use App\Tenant;
use App\Guardian;
use App\NimbusEdu\NimbusEdu;
use App\StudentGrade;
use Illuminate\Support\Facades\Log;

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
    $nimbus_edu = new NimbusEdu(Tenant::find(1));

    $studentGrades = StudentGrade::ofTenant($nimbus_edu->tenant->id);
  
    foreach($studentGrades as $studentGrade){
      $students = factory(App\Student::class, 10)
        ->create([
          'tenant_id' => $nimbus_edu->tenant->id,
          'meta' => [
            'student_grade_id' => $studentGrade
          ]
        ])
        ->each(function($student) use ($nimbus_edu, $studentGrade, $count){

          $parent = factory(Guardian::class)
            ->create([
              'tenant_id' => $nimbus_edu->tenant->id,
            ]);

          $parent->assignWards([$student->id]);

          $nimbus_edu->enrollCoreCourses($student, $studentGrade);

          $count++;
        });
      
      Log::info('Created '.$students->count().' students for student_grade '.$studentGrade);
      var_dump('Created '.$students->count().' students for student_grade '.$studentGrade);
    }
  }
}
