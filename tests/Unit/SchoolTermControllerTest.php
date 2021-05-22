<?php

namespace Tests\Unit;

use App\Nimbus\Institution;
use App\SchoolTerm;
use DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\Auth\SetupUser;

class SchoolTermControllerTest extends TestCase
{
	use RefreshDatabase, SetupUser, WithoutMiddleware;
	/**
   * Get a tenants payment profiles
   *
   * @return void
   */
  public function testGetSchoolTerms()
  {
    $this->seed(DatabaseSeeder::class);
		$institution = new Institution();
    $schoolTerm = $institution->newSchoolTerm($this->user->tenant, 'first term');
		$response = $this->actingAs($this->user)
      ->getJson('api/v1/school_terms');

    $response->assertStatus(200)
			->assertJson([
				"data" => [
					[
						"id" => $schoolTerm->id,
						"name" => $schoolTerm->name,
					],
				],
			]);
  }

	/**
   * Get a tenants payment profiles
   *
   * @return void
   */
  public function testCreateSchoolTerm()
  {
    $this->seed(DatabaseSeeder::class);
		$data = factory(SchoolTerm::class)->make([
			'type_id' => $this->user->tenant->schoolTermTypes->first()->id,
			'tenant_id' => $this->user->tenant->id,
		]);
		$response = $this->actingAs($this->user)
      ->postJson('api/v1/school_terms', $data->toArray());
		$schoolTerm = SchoolTerm::first();

		$response->assertStatus(200)
			->assertJson([
				"id" => $schoolTerm->id,
				"name" => $schoolTerm->name,
			]);
  }
}
