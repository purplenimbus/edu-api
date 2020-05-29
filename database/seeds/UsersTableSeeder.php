<?php
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
use App\Tenant;
use App\Guardian;
use App\Nimbus\NimbusEdu;

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

    $course_grades = [7,8,9,10,11,12,13];
  
    foreach($course_grades as $course_grade){
      $students = factory(App\Student::class, 'student', 10)
        ->create([
          'tenant_id' => $nimbus_edu->tenant->id,
          'meta' => [
            'course_grade_id' => $course_grade
          ]
        ])
        ->each(function($student) use ($nimbus_edu, $course_grade, $count){

          $parent = factory(Guardian::class, 'parent')
            ->create([
              'tenant_id' => $nimbus_edu->tenant->id,
            ]);

          $parent->assignWard($student);

          $nimbus_edu->enrollCoreCourses($student, $course_grade);

          $count++;
        });
      
      \Log::info('Created '.$students->count().' students for course_grade '.$course_grade);
      var_dump('Created '.$students->count().' students for course_grade '.$course_grade);
    }
  }
}
