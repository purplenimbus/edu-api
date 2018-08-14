<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->call('CoursesSeeder');
		$this->call('UsersTableSeeder');
		$this->call('OtherSeeders');

    }
}
