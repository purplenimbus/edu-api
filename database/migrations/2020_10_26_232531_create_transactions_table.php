<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('transactions', function (Blueprint $table) {
			$table->id();
			$table->decimal('amount', 13, 2);
			$table->string('authorization_code')->nullable();
			$table->dateTime('paid_at')->nullable();
			$table->integer('invoice_id');
			$table->integer('tenant_id');
			$table->integer('status_id')->default(1);
			$table->unsignedInteger('ref_id')->nullable()->unique();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('transactions');
	}
}
