<?php

namespace Tests\Unit\Models;

use App\BankAccount;
use App\PaymentProfile;
use App\SchoolTerm;
use App\Student;
use App\Tenant;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantTest extends TestCase
{
  use RefreshDatabase;
  /**
   * A tenant has many students
   *
   * @return void
   */
  public function testHasManyStudents()
  {
    $tenant1 = factory(Tenant::class)->create();
    factory(Student::class, 3)->create([
      'tenant_id' => $tenant1->id,
    ]);
    $tenant2 = factory(Tenant::class)->create();
    factory(Student::class, 2)->create([
      'tenant_id' => $tenant2->id,
    ]);

    $this->assertEquals(3, $tenant1->students->count());
    $this->assertEquals(2, $tenant2->students->count());
  }

  /**
   * A tenant has many payment profiles
   *
   * @return void
   */
  public function testHasManyPaymentProfiles()
  {
    $tenant = factory(Tenant::class)->create();
    factory(PaymentProfile::class, 3)->create([
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals(3, $tenant->payment_profiles->count());
  }

  /**
   * A tenant has many school term types
   *
   * @return void
   */
  public function testHasManySchoolTermTypes()
  {
    $tenant = factory(Tenant::class)->create();

    $this->assertEquals(3, $tenant->schoolTermTypes->count());
  }

  /**
   * A tenant has many bank accounts
   *
   * @return void
   */
  public function testHasManyBankAccounts()
  {
    $tenant = factory(Tenant::class)->create();
    factory(BankAccount::class, 3)->create([
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals(3, $tenant->bank_accounts->count());
  }

  /**
   * A tenant has an owner
   *
   * @return void
   */
  public function testHasAnOwner()
  {
    $tenant = factory(Tenant::class)->create();
    $owner = factory(User::class)->create([
      'tenant_id' => $tenant->id
    ]);
    $tenant->setOwner($owner);

    $this->assertEquals($owner->id, $tenant->owner->id);
  }

  /**
   * A tenant has an email
   *
   * @return void
   */
  public function testHasAnEmail()
  {
    $tenant = factory(Tenant::class)->create();
    $owner = factory(User::class)->create([
      'tenant_id' => $tenant->id
    ]);
    $tenant->setOwner($owner);

    $this->assertEquals($owner->email, $tenant->email);
  }

  /**
   * A tenant has a default bank account
   *
   * @return void
   */
  public function testHasADefaultBankAccount()
  {
    $tenant = factory(Tenant::class)->create();
    $bankAccount = factory(BankAccount::class)->create([
      'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals($bankAccount->id, $tenant->default_bank_account->id);
  }

  /**
   * A tenant has a current term
   *
   * @return void
   */
  public function testHasACurrentTerm()
  {
    $tenant = factory(Tenant::class)->create();
    $schoolTerm = factory(SchoolTerm::class)->create([
      'tenant_id' => $tenant->id,
      'current_term' => true,
    ]);

    $this->assertEquals($schoolTerm->id, $tenant->current_term->id);
  }
}
