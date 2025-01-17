<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('customer_name');
            $table->string('contact_number')->unique();
            $table->dateTime('pick_up_date_time');
            $table->string('pick_up_location');
            $table->dateTime('drop_off_date_time');
            $table->string('drop_off_location');
            $table->string('car_model');
            $table->string('car_type');
            $table->string('lead_source');
            $table->enum('status',['new','attempted_to_contacted','confirmed','cancelled','lost_lead','junk_lead']);
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
        Schema::dropIfExists('leads');
    }
}
