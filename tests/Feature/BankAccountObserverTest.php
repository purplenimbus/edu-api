<?php

namespace Tests\Feature;

use App\BankAccount;
use App\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Unicodeveloper\Paystack\Facades\Paystack;
use Tests\Feature\Helpers\Auth\SetupUser;

class BankAccountObserverTest extends TestCase
{
  use RefreshDatabase, WithFaker, SetupUser;

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
    factory(BankAccount::class)->create([
      "account_name" => $this->user->full_name,
      "default" => true,
      "tenant_id" => $this->user->tenant->id,
    ]);
    factory(BankAccount::class)->create([
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

  /**
   * Create a new sub account when a bank account is created
   *
   * @return void
   */
  public function testCreatesNewSubAccountWhenBankAccountCreated()
  {
    $this->user->tenant->setOwner($this->user);

    Paystack::shouldReceive("createSubAccount")
      ->andReturn([
        "data" => [
          "subaccount_code" => "abcdef",
        ],
      ]);

    $response = $this->actingAs($this->user)
      ->postJson("api/v1/tenants/{$this->user->tenant->id}/bank_accounts", [
        "account_name" => $this->user->full_name,
        "account_number" => "0038445618",
        "bank_code" => "055",
        "bank_name" => "gt bank",
        "description" => "test",
      ]);

    $response->assertStatus(200);

    $this->assertEquals("abcdef", Tenant::first()->subaccount_code);
  }
}
