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
        $access_levels = [
			[
				'name' => 'guest',
			],[
				'name' => 'user',
			],[
				'name' => 'admin',
			],[
				'name' => 'super admin'
			]
		];
		
		foreach($access_levels as $level){
			App\AccessLevel::create($level);
		}
		
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

		$user_types = [
			[	'name' => 'guest'	],
			[	'name' => 'student'	],
			[	'name' => 'teacher'	],
			[	'name' => 'parent'	],
			[	'name' => 'alumni'	],
			[	'name' => 'other'	]
		];
		
		//Create Currencies
		foreach($user_types as $user_type){
			App\UserType::create($user_type);
		}

		$curricula_types = [
			[	'country' => 'nigeria'	],
		];
		
		//Create Currencies
		foreach($curricula_types as $curricula_type){
			App\CurriculumType::create($curricula_type);
		}

		$account_status_types = [
			[	'name' => 'created'	],
			[	'name' => 'registered'	],
			[	'name' => 'assigned'	],
			[	'name' => 'terminated'	],
			[	'name' => 'archived'	],
		];
		
		//Create Currencies
		foreach($account_status_types as $account_status_type){
			App\StatusType::create($account_status_type);
		}

		$billing_status_types = [
			[	'name' => 'pending'	],
			[	'name' => 'completed'	],
			[	'name' => 'cancelled'	],
			[	'name' => 'archived'	],
		];
		
		//Create Currencies
		foreach($billing_status_types as $billing_status_type){
			App\BillingStatus::create($billing_status_type);
		}
    }
}
