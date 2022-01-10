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
      ->getJson("api/v1/tenants/" . $this->user->tenant_id)
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
      ->putJson("api/v1/tenants/" . $this->user->tenant_id, [
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
    Storage::fake("s3");

    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/" . $this->user->tenant_id, [
        "logo" => UploadedFile::fake()->image("test.jpg"),
      ])
      ->assertJson([
        "logo" => "/storage/tenant_avatars/{$this->user->tenant_id}.jpeg"
      ]);
  }

  public function testItDeletesAValidTenantsLogo()
  {
    Storage::fake("s3");

    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/" . $this->user->tenant_id, [
        "logo" => null,
      ])
      ->assertJson([
        "logo" => null
      ]);
  }

  public function testItReturnsATenantsValidSettings()
  {
    $this->actingAs($this->user)
      ->getJson("api/v1/tenants/" . $this->user->tenant_id . "/settings?name=course_schema")
      ->assertJson([
        [
          "name" => "midterm 1",
          "score" => 20,
        ],
        [
          "name" => "midterm 2",
          "score" => 20,
        ],
        [
          "name" => "midterm 3",
          "score" => 20,
        ],
        [
          "name" => "exam",
          "score" => 40,
        ]
      ]);
  }

  public function testItDosentReturnATenantsInvalidSettings()
  {
    $this->actingAs($this->user)
      ->getJson("api/v1/tenants/" . $this->user->tenant_id . "/settings?name=course_schemas")
      ->assertStatus(422)
      ->assertJson([
        "errors" => [
          "name" => ["The selected name is invalid."]
        ]
      ]);
  }

  public function testItUpdatesATenantsCourseSchemaSettings()
  {
    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/" . $this->user->tenant_id . "/settings", [
        "name" => "course_schema",
        "value" => [
          [
            "name" => "midterm 1",
            "score" => 50,
          ],
          [
            "name" => "exam",
            "score" => 50,
          ],
        ],
      ])
      ->assertOk()
      ->assertJson([
        [
          "name" => "midterm 1",
          "score" => 50,
        ],
        [
          "name" => "exam",
          "score" => 50,
        ],
      ]);
  }

  public function testItDoesntUpdateATenantsCourseSchemaSettingsWithAScoreOver100()
  {
    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/" . $this->user->tenant_id . "/settings", [
        "name" => "course_schema",
        "value" => [
          [
            "name" => "midterm 1",
            "score" => 500,
          ],
        ],
      ])
      ->assertStatus(422)
      ->assertJson([
        "errors" => [
          "value" => ["The sum of the course scores must be 100"],
          "value.0.score" => ["The value.0.score may not be greater than 100."],
        ]
      ]);
  }

  public function testItDoesntUpdateATenantsCourseSchemaSettingsWithAScoreSumOver100()
  {
    $this->actingAs($this->user)
      ->putJson("api/v1/tenants/" . $this->user->tenant_id . "/settings", [
        "name" => "course_schema",
        "value" => [
          [
            "name" => "midterm 1",
            "score" => 50,
          ],
          [
            "name" => "midterm 2",
            "score" => 60,
          ],
        ],
      ])
      ->assertStatus(422)
      ->assertJson([
        "errors" => [
          "value" => ["The sum of the course scores must be 100"],
        ]
      ]);
  }
}
