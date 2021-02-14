<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchoolTermTypesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('school_term_types', function (Blueprint $table) {
      $table->id();
      $table->dateTime('end_date');
			$table->dateTime('start_date');
      $table->integer('tenant_id');
			$table->mediumText('description')->nullable();
			$table->string('name');
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
    Schema::dropIfExists('school_term_types');
  }
}
