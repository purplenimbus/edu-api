<?php

namespace Tests\Unit\Models;

use App\Guardian;
use App\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianTest extends TestCase
{
  use RefreshDatabase;

  public function testItHasManyWards()
  {
    $guardian = factory(Guardian::class)->create();
    $student1 = factory(Student::class)->create();
    $student2 = factory(Student::class)->create();
    $guardian->assignWards([$student1->id, $student2->id]);

    $this->assertEquals([
      $student1->id,
      $student2->id,
    ], $guardian->wards()->pluck('id')->toArray());
  }
}
