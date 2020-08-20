<?php

namespace App\Http\Controllers;

use App\BankAccount;
use App\Http\Requests\DeleteBankAccount;
use App\Http\Requests\GetBankAccounts;
use App\Http\Requests\StoreBankAccount;
use App\Http\Requests\UpdateBankAccounts;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\QueryBuilder;

class BankAccountController extends Controller
{
	/**
   * List bank accounts
   *
   * @return void
   */
  public function index(GetBankAccounts $request)
  {
		$bank_accounts = QueryBuilder::for(BankAccount::class)
      ->defaultSort('bank_name')
      ->allowedSorts(
        'bank_name',
        'created_at',
        'updated_at'
			)->where([
        ['tenant_id', '=', $request->tenant_id],
      ])
      ->paginate($request->paginate ?? config('edu.pagination'));

    return response()->json($bank_accounts, 200);   
	}

	/**
   * Update a bank account
   *
   * @return void
   */
	public function update(UpdateBankAccounts $request){
		$bank_account = BankAccount::find($request->bank_account_id);

		$bank_account->fill($request->all());
		
		$bank_account->save();
    
    return response()->json($bank_account, 200);
	}
	
	/**
   * Create a new bank account
   *
   * @return void
   */
  public function create(StoreBankAccount $request){    
		$data = $request->all();

		$data['tenant_id'] = $request->tenant_id;

    $account = BankAccount::updateOrCreate(Arr::only($data, ['account_number', 'bank_code']), Arr::except($data, ['account_number', 'bank_code']));
    
    return response()->json($account, 200);
	}
	
	/**
   * Delete a new bank account
   *
   * @return void
   */
  public function delete(DeleteBankAccount $request){
    BankAccount::destroy($request->bank_account_id);

		return response()->json(true, 200);
  }
}
