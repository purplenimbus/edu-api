<?php

namespace Tests\Unit;

use App\Tenant;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;
use Tests\Helpers\SetupUser;

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
    $this->user->update(['firstname' => 'anthony']);
    $user1 = factory(User::class)->create([
      'firstname' => 'james',
      'tenant_id' => $this->user->tenant_id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    $user2 = factory(User::class)->create([
      'firstname' => 'benjamin',
      'tenant_id' => $tenant2->id,
    ]);

    $this->actingAs($this->user)
      ->getJson('api/v1/users')
      ->assertStatus(200)
      ->assertJson([
        "data" => [
          $this->user->only(['id', 'firstname']),
          $user2->only(['id', 'firstname']),
          $user1->only(['id', 'firstname']),
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
      'tenant_id' => $tenant2->id,
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[firstname]={$user1->firstname}")
      ->assertStatus(200)
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
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[lastname]={$user1->lastname}")
      ->assertStatus(200)
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
      'firstname' => 'james',
      'tenant_id' => $this->user->tenant_id,
    ]);
    $this->user->update(['firstname' => 'anthony']);
    $tenant2 = factory(Tenant::class)->create();
    factory(User::class)->create([
      'tenant_id' => $tenant2->id,
      'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQi1SYU1kgu3FtGlMpm5W7K2zuZHLgBQZzf34TQ3_Qe8LUd8s5C',
    ]);
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[has_image]=false")
      ->assertStatus(200)
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
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[has_image]=true")
      ->assertStatus(200)
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
      'tenant_id' => $tenant2->id,
    ]);

    $this->actingAs($this->user)
      ->getJson("api/v1/users?filter[account_status]={$user1->account_status_id}")
      ->assertStatus(200)
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
    
    $this->actingAs($this->user)
      ->getJson("api/v1/users/{$user1->id}")
      ->assertStatus(200)
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

    $this->actingAs($this->user)
      ->putJson("api/v1/users/{$user1->id}", [
        'firstname' => 'melinda',
        'lastname' => 'epifano',
      ])
      ->assertStatus(200)
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
          'address',
          'email',
          'firstname',
          'lastname',
          'tenant_id',
        ])
      )->assertStatus(200);
    
    $user = User::all()->last();

    $response
      ->assertJson([
        "id" => $user->id,
        "firstname" => $user->firstname,
        "lastname" => $user->lastname,
      ]);
  }
}
