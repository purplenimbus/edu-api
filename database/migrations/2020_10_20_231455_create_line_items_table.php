<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineItemsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('line_items', function (Blueprint $table) {
			$table->float('amount');
			$table->increments('id');
			$table->integer('invoice_id');
			$table->integer('tenant_id');
			$table->integer('quantity');
			$table->mediumText('description');
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
		Schema::dropIfExists('line_items');
	}
}
