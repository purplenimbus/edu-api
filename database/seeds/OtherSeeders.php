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
    }
}
