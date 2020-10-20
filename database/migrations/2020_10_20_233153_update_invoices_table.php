<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateInvoicesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('invoices', function (Blueprint $table) {
			$table->string('invoice_number')->nullable()->unique();
			$table->date('due_date')->nullable();
			$table->mediumText('comments')->nullable();
			$table->integer('user_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('invoices', function (Blueprint $table) {
			$table->dropColumn('comments');
			$table->dropColumn('due_date');
			$table->dropColumn('invoice_number');
			$table->dropColumn('user_id');
		});
	}
}
