<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentProfileItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_profile_items', function (Blueprint $table) {
			$table->id();
			$table->integer('tenant_id');
			$table->mediumText('description')->nullable(); 
			$table->decimal('amount', 13, 2);
			$table->integer('payment_profile_id');
			$table->integer('type_id');
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
		Schema::dropIfExists('payment_profile_items');
	}
}
