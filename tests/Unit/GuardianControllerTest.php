<?php

namespace Tests\Unit;

use App\Guardian;
use App\Student;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\Helpers\SetupUser;
use Tests\TestCase;

class GuardianControllerTest extends TestCase
{
  use SetupUser, RefreshDatabase, WithoutMiddleware;

  public function testItReturnsPaginatedGuardiansSortedByFirstNameInAscendingOrdeByDefault()
  {
    $guardian1 = factory(Guardian::class)->create([
      "firstname" => "marc",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians")
      ->assertOk();

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian2->id],
        ["id" => $guardian1->id]
      ],
      "per_page" => 10,
      "total" => 2,
    ]);
  }

  public function testItReturnsPaginatedGuardiansSortedByFirstNameInDescendingOrder()
  {
    $guardian1 = factory(Guardian::class)->create([
      "firstname" => "marc",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?sort=-firstname")
      ->assertOk();

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian1->id],
        ["id" => $guardian2->id]
      ],
      "per_page" => 10,
      "total" => 2,
    ]);
  }

  public function testItReturnsPaginatedGuardiansSortedByLastNameInAscendingOrder()
  {
    $guardian1 = factory(Guardian::class)->create([
      "lastname" => "marc",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "lastname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?sort=lastname")
      ->assertOk();

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian2->id],
        ["id" => $guardian1->id]
      ],
      "per_page" => 10,
      "total" => 2,
    ]);
  }

  public function testItReturnsPaginatedGuardiansSortedByLastNameInDescendingOrder()
  {
    $guardian1 = factory(Guardian::class)->create([
      "lastname" => "marc",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "lastname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?sort=-lastname")
      ->assertOk();

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian1->id],
        ["id" => $guardian2->id]
      ],
      "per_page" => 10,
      "total" => 2,
    ]);
  }

  public function testItReturnsPaginatedGuardiansFilteredByFirstName()
  {
    factory(Guardian::class)->create([
      "firstname" => "marc",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?filter[firstname]=ant")
      ->assertOk();

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian2->id]
      ],
      "per_page" => 10,
      "total" => 1,
    ]);
  }

  public function testItReturnsPaginatedGuardiansFilteredByLastName()
  {
    factory(Guardian::class)->create([
      "firstname" => "marc",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "lastname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?filter[lastname]=ant")
      ->assertOk();

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian2->id]
      ],
      "per_page" => 10,
      "total" => 1,
    ]);
  }

  public function testItReturnsPaginatedGuardiansFilteredByEmail()
  {
    factory(Guardian::class)->create([
      "email" => "manthony@yopmail.com",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "email" => "aakpan@yopmail.com",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?filter[email]=aakpan")
      ->assertOk();

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian2->id]
      ],
      "per_page" => 10,
      "total" => 1,
    ]);
  }

  public function testItReturnsPaginatedGuardianWithImages()
  {
    $guardian = factory(Guardian::class)->create([
      "image" => "/image.jpg",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Guardian::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?filter[has_image]=true");

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian->id]
      ],
      "per_page" => 10,
      "total" => 1,
    ])
      ->assertOk();
  }

  public function testItReturnsPaginatedGuardiansWithoutImages()
  {
    factory(Guardian::class)->create([
      "image" => "/image.jpg",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?filter[has_image]=false");

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["id" => $guardian2->id]
      ],
      "per_page" => 10,
      "total" => 1,
    ])
      ->assertOk();
  }

  public function testItDoesentReturnFilteredPaginatedGuardiansWithOutMatches()
  {
    factory(Guardian::class)->create([
      "firstname" => "marc",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Guardian::class)->create([
      "lastname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?filter[lastname]=bad");

    $response->assertJson([
      "current_page" => 1,
      "data" => [],
      "per_page" => 10,
      "total" => 0,
    ])
      ->assertOk();
  }

  public function testItReturnsPaginatedGuardiansWithAccountStatus()
  {
    factory(Guardian::class)->create([
      "image" => "/image.jpg",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Guardian::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians?include=status");

    $response->assertJson([
      "current_page" => 1,
      "data" => [
        ["status" => 'created'],
        ["status" => 'created']
      ],
      "per_page" => 10,
      "total" => 2,
    ])
      ->assertOk();
  }

  public function testItReturnsPaginatedGuardiansWithTheirWards()
  {
    $student1 = factory(Student::class)->create([
      "firstname" => "anthony",
    ]);
    $student2 = factory(Student::class)->create([
      "firstname" => "bezos",
    ]);
    $student3 = factory(Student::class)->create([
      "firstname" => "james",
    ]);
    $student4 = factory(Student::class)->create([
      "firstname" => "zero",
    ]);
    $guardian1 = factory(Guardian::class)->create([
      "firstname" => "anthony",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian2 = factory(Guardian::class)->create([
      "firstname" => "xena",
      "tenant_id" => $this->user->tenant->id,
    ]);
    $guardian1->assignWards([
      $student1->id,
      $student2->id,
    ]);
    $guardian2->assignWards([
      $student3->id,
      $student4->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/guardians?append=wards")
      ->assertOk()
      ->assertJson([
        "data" => [
          [
            "id" => $guardian1->id,
            "wards" => [
              [
                "user_id" => $student1->id,
              ],
              [
                "user_id" => $student2->id,
              ]
            ]
          ],
          [
            "id" => $guardian2->id,
            "wards" => [
              [
                "user_id" => $student3->id,
              ],
              [
                "user_id" => $student4->id,
              ]
            ]
          ]
        ]
      ]);
  }

  public function testItReturnsAValidGuardian()
  {
    $guardian1 = factory(Guardian::class)->create([
      "image" => "/image.jpg",
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(Guardian::class)->create([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/guardians/{$guardian1->id}")
      ->assertOk();

    $response->assertJson([
      "id" => $guardian1->id
    ])->assertOk();
  }

  public function testItDoesntReturnAnInvalidGuardian()
  {
    $this->actingAs($this->user)
      ->getJson("api/v1/guardians/0")
      ->assertStatus(422);
  }

  public function testItCreatesAValidGuardian()
  {
    $data = factory(Guardian::class)->make([
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->postJson("api/v1/guardians/", $data->toArray())
      ->assertOk()
      ->assertJson([
        "email" => $data->email,
        "firstname" => $data->firstname,
      ]);
  }

  public function testItDoesntCreateAnInvalidGuardian()
  {
    $this->actingAs($this->user)
      ->postJson("api/v1/guardians/", [
        "firstname" => 'name'
      ])
      ->assertStatus(422);
  }

  public function testItUpdatesAValidGuardian()
  {
    $guardian = factory(Guardian::class)->create([
      'firstname' => 'davie',
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->putJson("api/v1/guardians/{$guardian->id}", [
        "firstname" => "jones"
      ])
      ->assertOk()
      ->assertJson([
        "firstname" => "jones",
      ]);

    $this->assertEquals('jones', $guardian->refresh()->firstname);
  }

  public function testItDoesntUpdateAnInvalidGuardian()
  {
    $this->actingAs($this->user)
      ->putJson("api/v1/guardians/0", [
        "firstname" => "jones"
      ])
      ->assertStatus(422);
  }

  public function testItDeletesAValidGuardian()
  {
    $guardian = factory(Guardian::class)->create([
      'firstname' => 'davie',
      "tenant_id" => $this->user->tenant->id,
    ]);

    $this->actingAs($this->user)
      ->deleteJson("api/v1/guardians/{$guardian->id}")
      ->assertOk();
  }

  public function testItDoesntDeleteAnInvalidGuardian()
  {
    $this->actingAs($this->user)
      ->deleteJson("api/v1/guardians/0")
      ->assertStatus(422);
  }
}
