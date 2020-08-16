<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('tenants', function (Blueprint $table) {
      $table->increments('id');
      $table->string('name');
      $table->json('address')->nullable();
      $table->string('country')->default(config('edu.default.country'));
      $table->string('paystack_id')->nullable();
      $table->string('paystack_code')->nullable();
      $table->string('card_brand')->nullable();
      $table->string('card_last_four', 4)->nullable();
      $table->timestamp('trial_ends_at')->nullable();
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
    Schema::dropIfExists('tenants');
  }
}
