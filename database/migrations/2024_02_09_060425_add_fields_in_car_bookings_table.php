<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsInCarBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('car_bookings', function (Blueprint $table) {
            $table->float('per_day_rental_charges')->nullable();
            $table->float('number_of_days')->nullable();
            $table->float('pickup_charges')->nullable();
            $table->float('dropoff_charges')->nullable();
            $table->float('discount')->nullable()->nullable();
            $table->float('total_booking_amount')->nullable();
            $table->float('refundable_security_deposit')->nullable();
            $table->float('due_at_delivery')->nullable();
            $table->text('booking_remarks')->nullable();
            $table->float('agent_commission')->nullable();
            $table->float('agent_commission_received')->nullable();
            $table->string('customer_mobile_country_code')->nullable();
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
