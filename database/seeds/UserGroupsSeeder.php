<?php

use Illuminate\Database\Seeder;
use App\Student;
use App\Guardian;
use App\UserGroup;
use App\UserGroupMember;
use App\Tenant as Tenant;
use App\Nimbus\NimbusEdu;

class UserGroupsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
		$tenant = Tenant::find(1);
		$nimbus_edu = new NimbusEdu($tenant);

		$parents = factory(Guardian::class, 'parent', 1)
      ->create([
        'tenant_id' => $nimbus_edu->tenant->id,
      ])
      ->each(function($parent) use ($nimbus_edu) {
        $students = factory(Student::class, 'student', 3)
	        ->create([
	          'tenant_id' => $nimbus_edu->tenant->id,
	        ])
	        ->each(function($student) use ($parent) {
	        	$parent->assignWard($student);
	        });
      });
  }
}
