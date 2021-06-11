<?php

namespace Tests\Unit;

use App\SchoolTerm;
use App\Tenant;
use Tests\TestCase;

class SchoolTermObserverTest extends TestCase
{
  /**
   * A basic unit test example.
   *
   * @return void
   */
  public function testSetsTheSchoolTermStatus()
  {
    $tenant = factory(Tenant::class)->create();
    $schoolTerm = factory(SchoolTerm::class)->create([
      "tenant_id" => $tenant->id,
    ]);

    $this->assertEquals("in progress", $schoolTerm->status);
  }
}
