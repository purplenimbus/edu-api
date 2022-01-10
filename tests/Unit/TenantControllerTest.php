<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

class TenantControllerTest extends TestCase
{
  use SetupUser, RefreshDatabase, WithoutMiddleware;

  public function testItReturnsAValidTenant()
  {
    $this->actingAs($this->user)
      ->getJson("api/v1/tenants/".$this->user->tenant_id)
      ->assertJson([
        "id" => $this->user->tenant_id,
        "name" => $this->user->tenant->name,
      ]);
  }

  public function testItDoesntReturnAnInvalidTenant()
  {
    $this->actingAs($this->user)
      ->getJson("api/v1/tenants/0")
      ->assertStatus(422);
  }

  public function testItUpdatesAValidTenant()
  {
    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/".$this->user->tenant_id, [
        "name" => "new name"
      ])
      ->assertJson([
        "name" => "new name"
      ]);
  }

  public function testItDoesntUpdateAnInvalidTenant()
  {
    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/0", [
        "name" => "new name"
      ])
      ->assertStatus(422);
  }

  public function testItUpdatesAValidTenantsLogo()
  {
    Storage::fake('s3');

    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/".$this->user->tenant_id, [
        'logo' => UploadedFile::fake()->image("test.jpg"),
      ])
      ->assertJson([
        "logo" => "/storage/tenant_avatars/{$this->user->tenant_id}.jpeg"
      ]);
  }

  public function testItDeletesAValidTenantsLogo()
  {
    Storage::fake('s3');

    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/".$this->user->tenant_id, [
        'logo' => null,
      ])
      ->assertJson([
        "logo" => null
      ]);
  }

  public function testItReturnsATenantsSettings()
  {
    $this->user->tenant->update([
      'meta' => [
        'settings' => [
          'course_schema' => [
            [
              'name' => 'midterm 1',
              'score' => 20,
            ],
            [
              'name' => 'midterm 2',
              'score' => 20,
            ],
            [
              'name' => 'project 1',
              'score' => 20,
            ],
            [
              'name' => 'exam',
              'score' => 40,
            ]
          ]
        ]
      ]
    ]);
    $this->actingAs($this->user)
      ->getJson("api/v1/tenants/settings".$this->user->tenant_id)
      ->assertJson([
        "course_schema" => [
          [
            'name' => 'midterm 1',
            'score' => 20,
          ],
          [
            'name' => 'midterm 2',
            'score' => 20,
          ],
          [
            'name' => 'project 1',
            'score' => 20,
          ],
          [
            'name' => 'exam',
            'score' => 40,
          ]
        ]
      ]);
  }
}