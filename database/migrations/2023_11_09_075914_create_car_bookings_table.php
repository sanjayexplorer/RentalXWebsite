<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('car_bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('carId');
            $table->unsignedBigInteger('user_id');
            $table->string('customer_name')->nullable();
            $table->string('customer_mobile')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('pickup_location')->nullable();
            $table->string('dropoff_location')->nullable();
            $table->string('pickup_time')->nullable();
            $table->string('dropoff_time')->nullable();
            $table->double('advance_booking_amount')->nullable();
            $table->bigInteger('bookingId')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('car_bookings');
    }
}
