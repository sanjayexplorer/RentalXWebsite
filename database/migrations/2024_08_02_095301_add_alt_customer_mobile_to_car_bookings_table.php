<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAltCustomerMobileToCarBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_bookings', function (Blueprint $table) {
            $table->string('alt_customer_mobile')->nullable();
            $table->string('alt_customer_mobile_country_code')->nullable();
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
            $table->dropColumn('alt_customer_mobile');
            $table->dropColumn('alt_customer_mobile_country_code');
        });
    }
}
