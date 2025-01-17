<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarsBookingMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cars_booking_meta', function (Blueprint $table) {
            $table->id();
             $table->integer('bookingId');
            $table->text('meta_key');
            $table->text('meta_value');
              $table->enum('status',['active','inactive'])->nullable()->default('active');
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
        Schema::dropIfExists('cars_booking_meta');
    }
}
