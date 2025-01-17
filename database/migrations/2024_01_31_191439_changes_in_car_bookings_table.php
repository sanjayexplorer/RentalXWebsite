<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangesInCarBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_bookings', function (Blueprint $table) {
            $table->timestamp('pickup_date')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('dropoff_date')->nullable();
            $table->timestamp('end_date')->nullable();
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
            $table->dropColumn('pickup_date');
            $table->dropColumn('dropoff_date');
        });
    }
}
