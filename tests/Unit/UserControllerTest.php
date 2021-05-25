<?php

namespace Tests\Unit;

use App\Student;
use App\Tenant;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\Auth\SetupUser;

class UserControllerTest extends TestCase
{
  use RefreshDatabase, SetupUser, WithoutMiddleware;
  /**
   * Get all users for a tenant
   *
   * @return void
   */
  public function testGetUsers()
  {
    $user1 = factory(User::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      'tenant_id' => $tenant2,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson('api/v1/users');

    $response->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $this->user->id,
            "firstname" => $this->user->firstname,
          ],
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
          ],
        ],
      ]);
  }

  /**
   * Get all users filtered by first name for a tenant
   *
   * @return void
   */
  public function testGetUsersWithFirstNameFilter()
  {
    $user1 = factory(User::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      'tenant_id' => $tenant2,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[firstname]={$user1->firstname}");

    $response->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
          ],
        ],
      ]);
  }

  /**
   * Get all users filtered by last name for a tenant
   *
   * @return void
   */
  public function testGetUsersWithLastNameFilter()
  {
    $user1 = factory(User::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      'tenant_id' => $tenant2,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[lastname]={$user1->lastname}");

    $response->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
            "lastname" => $user1->lastname,
          ],
        ],
      ]);
  }

  /**
   * Get all users without and image for a tenant
   *
   * @return void
   */
  public function testFilterUsersWithoutImage()
  {
    $user1 = factory(User::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      'tenant_id' => $tenant2,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[has_image]=false");

    $response->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $this->user->id,
            "firstname" => $this->user->firstname,
          ],
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
          ],
        ],
      ]);
  }

  /**
   * Get all users with images for a tenant
   *
   * @return void
   */
  public function testFilterUsersWithImage()
  {
    $user1 = factory(User::class)->create([
      'tenant_id' => $this->user->tenant->id,
      'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQi1SYU1kgu3FtGlMpm5W7K2zuZHLgBQZzf34TQ3_Qe8LUd8s5C',
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      'tenant_id' => $tenant2,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[has_image]=true");

    $response->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
            "image" => $user1->image,
          ],
        ],
      ]);
  }

  /**
   * Filter all users by status for a tenant
   *
   * @return void
   */
  public function testFilterUsersbyAccountStatus()
  {
    $user1 = factory(User::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $user1->update([
      'account_status_id' => User::StatusTypes['archived'],
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      'tenant_id' => $tenant2,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[account_status]={$user1->account_status_id}");

    $response->assertStatus(200)
      ->assertJson([
        "data" => [
          [
            "id" => $user1->id,
            "firstname" => $user1->firstname,
          ],
        ],
      ]);
  }

  /**
   * Get a user by id
   *
   * @return void
   */
  public function testGetUserById()
  {
    $user1 = factory(User::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->getJson("api/v1/users/{$user1->id}");

    $response->assertStatus(200)
      ->assertJson([
        "id" => $user1->id,
        "firstname" => $user1->firstname,
      ]);
  }

  /**
   * Update a user
   *
   * @return void
   */
  public function testUpdateUserById()
  {
    $user1 = factory(User::class)->create([
      'tenant_id' => $this->user->tenant->id,
    ]);
    $response = $this->actingAs($this->user)
      ->putJson("api/v1/users/{$user1->id}", [
        'firstname' => 'melinda',
        'lastname' => 'epifano',
      ]);

    $response->assertStatus(200)
      ->assertJson([
        "id" => $user1->id,
        "firstname" => 'melinda',
        "lastname" => 'epifano',
      ]);
  }

  /**
   * Create a new user
   *
   * @return void
   */
  public function testCreateNewUser()
  {
    $data = factory(User::class)->make([
      'tenant_id' => $this->user->tenant->id,
      'address' => [
        'city' => 'springfield',
        'country' => 'united states of america',
        'state' => 'missouri',
        'street' => '1742 evergreen terrace',
      ]
    ]);

    $response = $this->actingAs($this->user)
      ->postJson(
        "api/v1/users/",
        $data->only([
          'email',
          'firstname',
          'lastname',
          'address'
        ])
      );
    
    $user = User::all()->last();

    $response->assertStatus(200)
      ->assertJson([
        "id" => $user->id,
        "firstname" => $user->firstname,
        "lastname" => $user->lastname,
      ]);
  }
}
