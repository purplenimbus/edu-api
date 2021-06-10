<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolTermsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('school_terms', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('tenant_id');
      $table->string('name');
      $table->json('meta')->nullable();
      $table->string('description')->nullable();
      $table->timestamps();
      $table->dateTime('start_date');
      $table->integer('status_id');
      $table->integer('type_id');
      $table->dateTime('end_date');
      $table->boolean('current_term')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('school_terms');
  }
}
