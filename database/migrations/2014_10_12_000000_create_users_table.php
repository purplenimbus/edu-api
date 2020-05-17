<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->increments('id');
      $table->integer('tenant_id');
      $table->date('date_of_birth');
      $table->string('firstname');
      $table->string('lastname');
      $table->string('othernames')->nullable();
      $table->string('title')->nullable();
      $table->string('password')->nullable();
      $table->string('email')->unique();
      $table->string('image')->nullable();
      $table->json('meta')->nullable();
      $table->json('address')->nullable();
      $table->integer('account_status_id')->default(1);
      $table->string('ref_id')->nullable();
      $table->rememberToken();
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
    Schema::dropIfExists('users');
  }
}
