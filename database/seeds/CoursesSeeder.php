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
				'code' => 'MATH100',
			],[
				'name' => 'english',
				'code' => 'ENG050',
			],[
				'name' => 'physics',
				'code' => 'PHY150',
			],[
				'name' => 'chemistry',
				'code' => 'CHEM100',
			],[
				'name' => 'biology',
				'code' => 'BIO100',
			]
		];
		
		foreach($subjects as $subject){
			App\Subject::create(['name' => $subject['name']]);
		}
		
		foreach($subjects as $course){
			factory(App\Course::class)->create([
						'name' => $course['name'],
						'code' => $course['code'],
						'tenant_id' => 1,
						'meta'	=>	[
							'course_schema' =>	[
								'quiz' =>  15,
								'midterm' => 30,
								'assignment' => 15,
								'lab' => 5,
								'exam' => 35
							]
						]
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
