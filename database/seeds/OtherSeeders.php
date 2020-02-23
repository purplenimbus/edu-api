<?php

use Illuminate\Database\Seeder;

class OtherSeeders extends Seeder
{
	/**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
		$currencies = [
			[
				'shortname' => 'NGN',
				'longname'  => 'naira',
				'symbol'	=>	'₦'
			]
		];
		
		//Create Currencies
		foreach($currencies as $currency){
			App\Currency::create($currency);
		}

		$curricula_types = [
			[	'country' => 'nigeria'	],
		];
		
		//Create Curriculla Types
		foreach($curricula_types as $curricula_type){
			App\CurriculumType::create($curricula_type);
		}

		$account_status_types = [
			[	'name' => 'created'	],
			[	'name' => 'unenrolled'	],
			[	'name' => 'registered'	],
			[	'name' => 'assigned'	],
			[	'name' => 'terminated'	],
			[	'name' => 'archived'	],
		];
		
		//Create Account Status Types
		foreach($account_status_types as $account_status_type){
			App\StatusType::create($account_status_type);
		}

		$billing_status_types = [
			[	'name' => 'pending'	],
			[	'name' => 'paid'	],
			[	'name' => 'cancelled'	],
			[	'name' => 'archived'	],
		];
		
		//Create Biling Status Types
		foreach($billing_status_types as $billing_status_type){
			App\BillingStatus::create($billing_status_type);
		}
    }
}
