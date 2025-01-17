<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('registration_number')->unique();
            $table->enum('transmission',['manual','auto'])->default('manual');
            $table->enum('fuel_type',['petrol','diesel','electric','cng','hybrid','other']);
            $table->integer('manufacturing_year');
            $table->enum('car_type',['sedan','hatchback','compact_suv','suv','luxury','off_road']);
            $table->enum('roof_type',['normal','sunroof','moonroof','convertible']);
            $table->integer('price');
            $table->integer('seats')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
