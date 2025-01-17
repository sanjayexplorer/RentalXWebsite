<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConfirmationFieldsToCarBookings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_bookings', function (Blueprint $table) {
            $table->timestamp('delivered_time')->nullable();
            $table->timestamp('collected_time')->nullable();
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
            $table->dropColumn('delivered_time');
            $table->dropColumn('collected_time');
        });
    }
}
