<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentProfileGradesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('payment_profiles', function (Blueprint $table) {
      $table->integer('course_grade_id')->nullable();
      $table->integer('term_id')->nullable();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('payment_profiles', function (Blueprint $table) {
      $table->dropColumn('course_grade_id');
      $table->dropColumn('term_id');
    });
  }
}
