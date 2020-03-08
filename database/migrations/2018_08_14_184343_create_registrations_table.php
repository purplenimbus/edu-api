<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegistrationsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('registrations', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('tenant_id');
      $table->integer('user_id');
      $table->integer('course_id');
      $table->integer('term_id')->nullable();
      $table->integer('billing_id')->nullable();
      $table->integer('course_score_id')->nullable();
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
    Schema::dropIfExists('registrations');
  }
}
