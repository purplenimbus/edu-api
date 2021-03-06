<?php

use Illuminate\Database\Seeder;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $subjects = [
			[
				'name' => 'mathematics',
				'code' => 'MATH',
			],[
				'name' => 'english',
				'code' => 'ENG',
			],[
				'name' => 'physics',
				'code' => 'PHY',
			],[
				'name' => 'chemistry',
				'code' => 'CHEM',
			],[
				'name' => 'biology',
				'code' => 'BIO',
			]
		];
		
		foreach($subjects as $subject){
			App\Subject::create($subject);
		}
		
		foreach($subjects as $course){
			factory(App\Course::class)->create([
						'name' => $course['name'],
						'code' => $course['code'],
						'tenant_id' => 1,
						'schema'	=>	config('edu.default.course_schema')
					])
					->each(function($course){
						factory(App\Lesson::class,5)->create([
							'tenant_id' => 1,
							'course_id' => $course->id
						])->each(function($lesson)use($course){
							factory(App\Lesson::class,2)->create([
								'tenant_id' => 1,
								'parent_id' => $lesson->id,
								'course_id' => $course->id
							]);
							echo "New Lesson , id : ".$lesson->id." , for ".$course->code."\r\n";
							
						});
						
					});
		}
    }
}
