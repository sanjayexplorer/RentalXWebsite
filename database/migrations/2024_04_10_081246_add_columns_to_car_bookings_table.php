<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCarBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_bookings', function (Blueprint $table) {
            $table->string('booking_type')->nullable();
            $table->string('car_name')->nullable();
            $table->string('registration_number')->nullable();
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
            $table->dropColumn('booking_type');
            $table->dropColumn('car_name');
            $table->dropColumn('registration_number');
        });
    }
}

