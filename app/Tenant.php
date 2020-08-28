<?php

namespace App;

use App\Nimbus\Institution;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\SchoolTerm;
use App\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Wisdomanthoni\Cashier\Billable;
use Unicodeveloper\Paystack\Facades\Paystack;

class Tenant extends Model
{
  use Notifiable, Billable;

  /**
  * The attributes that are mass assignable.
  *
  * @var array
  */
  protected $fillable = [
    'address', 'name', 'subaccount_code'
  ];

  /**
  * Cast meta property to array
  *
  * @var object
  */

  protected $casts = [
    'address' => 'object',
  ];

  /**
   * The accessors to append to the model's array form.
   *
   * @var array
   */
  protected $appends = [
    'current_term'
  ];

  /**
  * The attributes excluded from the model's JSON form.
  *
  * @var array
  */
  protected $hidden = [
    'created_at', 'updated_at'
  ];

  /**
  *  Setup model event hooks
  */
  public static function boot()
  {
    parent::boot();
    self::created(function($model) {
      $institution = new Institution($model);
    });
  }

  public function getCurrentTermAttribute() {
    return SchoolTerm::where([
      'tenant_id' => $this->id,
      'status_id' => 1
    ])
    ->first();
  }

  public function getOwnerAttribute() {
    return User::whereIs('admin')
      ->where('tenant_id', $this->id)
      ->first();
  }

  public function setOwner(User $user) {
    if ($this->owner) {
      throw new ValidationException(__("validation.custom.admin.exists"));
    }

    $user->assign('admin');
  }

  public function activateSubscription() {
    try {
      $this->createAsPaystackCustomer([
        "email" => $this->owner->email,
        "first_name" => $this->owner->firstname,
        "last_name" => $this->owner->lastname,
      ]);

      $this->newSubscription(env('PAYSTACK_PLAN_NAME'), env('PAYSTACK_PLAN_ID'))
        ->create(null);

    } catch(Exception $e) {
      Log::error('Invalid Request', [
        'message' => $e->getMessage(),
      ]);
    }
  }

  public function defaultBankAccount() {
    return BankAccount::where([
      'tenant_id' => $this->id,
      'default' => 1,
    ])->first();
  }
 
  public function createSubAccount(array $options = []) {
    try {
      $bank_account = $this->defaultBankAccount();

      if ($bank_account && $bank_account->account_number && $bank_account->bank_code) {
        $payload = array_merge([
          'account_number' => strval($bank_account->account_number),
          'business_name' => $this->name,
          'settlement_bank' => strval($bank_account->bank_code),
          'percentage_charge' => floatval(env('PAYSTACK_PROCESSING_FEE_PERCENTAGE')),
          'primary_contact_email' => $this->owner->email,
          'primary_contact_name' => $this->owner->fullname,
        ], $options);

        $phone_number = Arr::get($this, 'owner.address.phone_number', null);
  
        if ($phone_number) {
          $payload["primary_contact_phone"] = $phone_number;
        }

        $sub_account = Paystack::createSubAccount($payload);

        $sub_account_code = Arr::get($sub_account, 'data.subaccount_code', null);

        if ($sub_account_code) {
          $this->update(['subaccount_code' => $sub_account_code]);
        }

        dd($sub_account);
      }

    } catch(Exception $e) {
      dd($e->getMessage());
      Log::error('Invalid Request', [
        'message' => $e->getMessage(),
      ]);
    }
  }
}
