<?php

namespace Tests\Unit\NimbusEdu;

use App\Curriculum;
use App\NimbusEdu\Institution;
use App\Tenant;
use OtherSeeders;
use SubjectsSeeder;
use Tests\TestCase;

class InstitutionTest extends TestCase
{
  public function testItGeneratesADefaultCurriculumForATenant() {
    $this->seed(SubjectsSeeder::class);
    $this->seed(OtherSeeders::class);

    $tenant = factory(Tenant::class)->create();
    $institution = new Institution();
    $institution->generateCurriculum($tenant);

    $this->assertEquals(Curriculum::ofTenant($tenant->id)->count(), 13);
  }
}