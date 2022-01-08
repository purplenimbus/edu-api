<?php

namespace Tests\Feature;

use App\SchoolTerm;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolTermObserverTest extends TestCase
{
  use RefreshDatabase;
  /**
   * Sets status_id when present
   *
   * @return void
   */
  public function testSetsTheSchoolTermStatusWhenPresent()
  {
    $tenant = factory(Tenant::class)->create();
    $schoolTerm = factory(SchoolTerm::class)->create([
      'status_id' => SchoolTerm::Statuses['complete'],
      "tenant_id" => $tenant->id,
    ]);

    $this->assertEquals("complete", $schoolTerm->status);
  }

  /**
   * Sets status_id when not present
   *
   * @return void
   */
  public function testSetsTheDefaultSchoolTermStatus()
  {
    $tenant = factory(Tenant::class)->create();
    $schoolTerm = factory(SchoolTerm::class)->create([
      "tenant_id" => $tenant->id,
    ]);

    $this->assertEquals("in progress", $schoolTerm->status);
  }
}
