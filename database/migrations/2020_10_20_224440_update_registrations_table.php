<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRegistrationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('registrations', function (Blueprint $table) {
			$table->renameColumn('billing_id', 'invoice_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('registrations', function (Blueprint $table) {
			$table->renameColumn('invoice_id', 'billing_id');
		});
	}
}
