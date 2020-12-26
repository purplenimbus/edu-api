<?php

namespace Tests\Feature;

use App\BankAccount;
use App\Nimbus\Institution;
use App\Tenant;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class BankAccountTest extends TestCase
{
  use RefreshDatabase, WithFaker;

  public $user;

  public function setUp(): void {
    parent::setUp();

    $tenant = factory(Tenant::class)->create();

    $this->user = factory(User::class)->create([
      'tenant_id' => $tenant->id
    ]);

    $this->user->markEmailAsVerified();

    $token = auth()->login($this->user);
  }

  /**
   * Set the currently logged in user for the application.
   *
   * @param  \Illuminate\Contracts\Auth\Authenticatable $user
   * @param  string|null                                $driver
   * @return $this
   */
  public function actingAs($user, $driver = null)
  {
    $token = JWTAuth::fromUser($user);

    $this->withHeader('Authorization', "Bearer {$token}");

    parent::actingAs($user, "api");
    
    return $this;
  }

  /**
   * Test the default bank account when a new bank account is created with no other bank accounts
   * 
   *
   * @return void
   */
  public function testSetsTheDefaultBankAccountWhenCreatedAndNoBankAccountExists()
  {    
    $response = $this->actingAs($this->user)
      ->postJson("api/v1/tenants/{$this->user->tenant->id}/bank_accounts", [
        "account_name" => $this->user->full_name,
        "account_number" => "0038445618",
        "bank_code" => "055",
        "bank_name" => "gt bank",
        "description" => "test"
      ]);
    

    $response->assertStatus(200);
    $response->assertJson([
      "default" => true,
    ]);
  }

  /**
   * Test the default bank account when a bank account is updated
   *
   * @return void
   */
  public function testSetsTheDefaultBankAccountWhenUpdated()
  {    
    $defaultBankAccount = factory(BankAccount::class)->create([
      "account_name" => $this->user->full_name,
      "default" => true,
      "tenant_id" => $this->user->tenant->id,
    ]);
    $otherBankAccount = factory(BankAccount::class)->create([
      "account_name" => $this->user->full_name,
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->putJson("api/v1/tenants/{$this->user->tenant->id}/bank_accounts/{$otherBankAccount->id}", [
        "default" => true
      ]);
    
    $defaultBankAccount->refresh();
    $otherBankAccount->refresh();

    $response->assertStatus(200);
    $this->assertEquals($defaultBankAccount->default, false);
    $this->assertEquals($otherBankAccount->default, true);
  }

  /**
   * Test the default bank account when a new bank account is created
   *
   * @return void
   */
  public function testPassTheDefaultBankAccountParameterAndBankAccountsExists()
  {    
    $bankAccount = factory(BankAccount::class)->create([
      "account_name" => $this->user->full_name,
      "default" => true,
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/tenants/{$this->user->tenant->id}/bank_accounts", [
        "account_name" => $this->user->full_name,
        "account_number" => "0038445618",
        "bank_code" => "055",
        "bank_name" => "gt bank",
        "description" => "test",
        "default" => true,
      ]);
    $bankAccount->refresh();

    $response->assertStatus(200);
    $response->assertJson([
      "default" => true,
    ]);
    $this->assertEquals($this->user
      ->tenant
      ->bank_accounts()
      ->orderBy('id', 'DESC')
      ->first()
      ->default,
      true
    );
    $this->assertEquals($bankAccount->default, false);
  }

  /**
   * Test the default bank account when a bank account is deleted
   *
   * @return void
   */
  public function testSetTheDefaultBankAccountParameterWhenDeleted()
  {    
    $defaultBankAccount = factory(BankAccount::class)->create([
      "account_name" => $this->user->full_name,
      "default" => true,
      "tenant_id" => $this->user->tenant->id,
    ]);
    $otherBankAccount = factory(BankAccount::class)->create([
      "account_name" => $this->user->full_name,
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->delete("api/v1/tenants/{$this->user->tenant->id}/bank_accounts/{$defaultBankAccount->id}");

    $response->assertStatus(200);

    $this->assertNotContains($defaultBankAccount->toArray(), BankAccount::all()->toArray());
    $this->assertEquals($otherBankAccount->default, true);
  }

  /**
   * Test the list of bank accounts for a tenant
   *
   * @return void
   */
  public function testReturnsTheBankAccounts()
  {    
    $defaultBankAccount = factory(BankAccount::class)->create([
      "account_name" => $this->user->full_name,
      "default" => true,
      "tenant_id" => $this->user->tenant->id,
    ]);
    $otherBankAccount = factory(BankAccount::class)->create([
      "account_name" => $this->user->full_name,
      "tenant_id" => $this->user->tenant->id,
    ]);

    $response = $this->actingAs($this->user)
      ->getJson("api/v1/tenants/{$this->user->tenant->id}/bank_accounts", [
        "default" => true
      ]);

    $response->assertStatus(200);
    $response->assertJsonCount(2, "data");
  }
}
