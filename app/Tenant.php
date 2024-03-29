<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\SchoolTerm;
use App\User;
use Exception;
use Glorand\Model\Settings\Traits\HasSettingsField;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Unicodeveloper\Paystack\Facades\Paystack;

class Tenant extends Model
{
  use Notifiable;
  use HasSettingsField;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'address',
    'name',
    'subaccount_code',
    'paystack_id', 
    'paystack_code',
    'logo',
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
    'email',
  ];

  /**
   * The attributes excluded from the model's JSON form.
   *
   * @var array
   */
  protected $hidden = [
    'created_at', 'updated_at'
  ];

  public function getCurrentTermAttribute()
  {
    return SchoolTerm::ofTenant($this->id)
      ->whereCurrentTerm(true)
      ->first();
  }

  public function getHasCurrentTermAttribute()
  {
    return !is_null($this->currentTerm);
  }

  public function getOwnerAttribute()
  {
    return User::whereIs('admin')
      ->where('tenant_id', $this->id)
      ->first();
  }

  public function getPaymentDetailsAttribute()
  {
    return $this->default_bank_account;
  }

  public function getEmailAttribute()
  {
    return Arr::get($this, 'owner.email', null);
  }

  public function setOwner(User $user)
  {
    if ($this->owner) {
      throw new ValidationException(__("validation.custom.admin.exists"));
    }

    $user->assign('admin');
  }

  public function getDefaultBankAccountAttribute()
  {
    return BankAccount::where([
      'tenant_id' => $this->id,
      'default' => 1,
    ])->first();
  }

  public function createPayStackCustomer(array $options = [])
  {
    if ($this->paystack_id || !$this->owner) {
      return $this;
    }

    $data = array_merge([
      'additional_info' => (object)[],
      'fname' => $this->owner->firstname,
      'lname' => $this->owner->lastname,
      'email' => $this->owner->email,
    ], $options);

    $phone_number = Arr::get($this, 'address.phone', null);

    if ($phone_number) {
      $data['phone'] = $phone_number;
    }

    request()->merge($data);

    $payStackCustomer = PayStack::createCustomer();

    $this->update([
      'paystack_id' => Arr::get($payStackCustomer, 'data.customer_code')
    ]);

    return $this;
  }

  public function updatePayStackCustomer(array $options = [])
  {
    if (!$this->paystack_id || !$this->owner) {
      return;
    }

    $data = array_merge([
      'fname' => $this->owner->firstname,
      'lname' => $this->owner->lastname,
      'email' => $this->owner->email
    ], $options);

    $phone_number = Arr::get($this, 'address.phone', null);

    if ($phone_number) {
      $data['phone'] = $phone_number;
    }

    request()->merge($data);

    PayStack::updateCustomer($this->paystack_id);

    return $this;
  }

  public function getAsPayStackCustomer(array $options = [])
  {
    if (!$this->paystack_id) {
      return;
    }

    return PayStack::fetchCustomer($this->paystack_id);
  }

  public function createSubAccount(array $options = [])
  {
    try {
      if ($this->subaccount_code) {
        return $this->getAsPayStackAccount();
      }

      $bank_account = $this->default_bank_account;

      if ($bank_account && $bank_account->account_number && $bank_account->bank_code) {
        request()->merge($this->getPaystackPayload()); // required cause Paystack uses the request object https://github.com/unicodeveloper/laravel-paystack/blob/a6e8c790b16a947e5d2369ad77d2082e892c326b/src/Paystack.php#L631

        $sub_account = Paystack::createSubAccount();

        $sub_account_code = Arr::get($sub_account, 'data.subaccount_code', null);

        if ($sub_account_code) {
          $this->update(['subaccount_code' => $sub_account_code]);
        }

        return $sub_account;
      }
    } catch (Exception $e) {
      Log::error('Invalid Request', [
        'message' => $e->getMessage(),
      ]);
    }
  }

  public function updateOrCreateSubAccount(array $options = [])
  {
    if (!$this->subaccount_code) {
      return $this->createSubAccount();
    }

    try {
      $bank_account = $this->default_bank_account;

      if ($bank_account && $bank_account->account_number && $bank_account->bank_code) {
        request()->merge($this->getPaystackPayload($options)); // required cause Paystack uses the request object https://github.com/unicodeveloper/laravel-paystack/blob/a6e8c790b16a947e5d2369ad77d2082e892c326b/src/Paystack.php#L631

        $sub_account = Paystack::updateSubAccount($this->subaccount_code);

        $sub_account_code = Arr::get($sub_account, 'data.subaccount_code', null);

        if ($sub_account_code) {
          $this->update(['subaccount_code' => $sub_account_code]);
        }

        return $this;
      }
    } catch (Exception $e) {
    }
  }

  public function getAsPayStackAccount()
  {
    if (!$this->subaccount_code) {
      return;
    }

    return Paystack::fetchSubAccount($this->subaccount_code);
  }

  private function getPaystackPayload(array $options = [])
  {
    $bank_account = $this->default_bank_account;

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

    return $payload;
  }

  public function getHasBankAccountAttribute(){
    return ($this->default_bank_account ? true : false);
  }

  public function bank_accounts()
  {
    return $this->hasMany('App\BankAccount');
  }

  //need to deprecate this relationship for the schoolTerms method below
  public function term_types()
  {
    return $this->hasMany('App\SchoolTermType');
  }

  public function schoolTermTypes()
  {
    return $this->hasMany('App\SchoolTermType');
  }

  public function payment_profiles()
  {
    return $this->hasMany('App\PaymentProfile');
  }

  public function students()
  {
    return $this->hasMany('App\Student');
  }
}
