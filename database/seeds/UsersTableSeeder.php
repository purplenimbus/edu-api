<?php
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
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

        $nimbus_edu = new NimbusEdu(1);

        $course_grades = [7,8,9,10,11,12,13];
		
		foreach($course_grades as $course_grade){
			$students = factory(App\User::class,'student',10)
				->create([
					'tenant_id' =>  $nimbus_edu->tenant->id,
					'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
					'meta' => ['course_grade_id'=>$course_grade]
				])
				->each(function($student)use($nimbus_edu,$course_grade,$count){
					
					$nimbus_edu->registerStudent($student,$course_grade);

					$count++;
				});
			
			\Log::info('Created '.$students->count().' students for course_grade '.$course_grade);
		}		

				
		/*$teachers = factory(App\User::class,'teacher',2)
			->create([
				'tenant_id' => $tenant->id,
				'image' =>	'https://www.victoria147.com/wp-content/uploads/2014/10/user-avatar-placeholder.png',
			])
			->each(function($teacher)use($tenant,$count){
				echo "Teacher Created".$teacher->uuid."\r\n";
				$count++;
			});
			
		\Log::info('Created '.$teachers->count().' teachers');*/
	}
}
