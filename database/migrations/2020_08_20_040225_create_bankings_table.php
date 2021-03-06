<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bank_accounts', function (Blueprint $table) {
			$table->increments('id');
			$table->string('bank_name', 100);
			$table->string('bank_code');
			$table->char('account_number', 10);
			$table->string('account_name');
			$table->integer('tenant_id');
			$table->text('description')->nullable();
			$table->boolean('default')->default(false);
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bank_accounts');
	}
}
