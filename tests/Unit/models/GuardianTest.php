<?php

namespace Tests\Unit\Models;

use App\Guardian;
use App\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuardianTest extends TestCase
{
  use RefreshDatabase;

  public function testItHasAyWardsAttribute()
  {
    $guardian = factory(Guardian::class)->create();
    $student1 = factory(Student::class)->create([
      'firstname' => 'anthony'
    ]);
    $student2 = factory(Student::class)->create([
      'firstname' => 'emmanuel'
    ]);
    $guardian->assignWards([$student1->id, $student2->id]);

    $this->assertEquals([
      $student1->id,
      $student2->id,
    ], $guardian->wards->pluck('user_id')->toArray());
  }
}
