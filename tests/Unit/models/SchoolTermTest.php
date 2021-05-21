<?php

namespace Tests\Unit\Models;

use App\Nimbus\Institution;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\Auth\SetupUser;

class SchoolTermTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;

  /**
   * Get a tenants payment profiles
   *
   * @return void
   */
  public function testRegisteredStudents()
  {
    $this->seed(DatabaseSeeder::class);
    $institution = new Institution();
    $institution->newSchoolTerm($this->user->tenant, 'first term');

    dd($this->user->tenant->current_term());
  }
}