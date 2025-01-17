<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingOwnerIdAndDriverIdToCarBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_bookings', function (Blueprint $table) {
            $table->integer('booking_owner_id')->nullable();
            $table->integer('driver_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('car_bookings', function (Blueprint $table) {
            $table->dropColumn('booking_owner_id');
            $table->dropColumn('driver_id');
        });
    }
}
