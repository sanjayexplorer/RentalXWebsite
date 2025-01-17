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
        Schema::create('cars_metas', function (Blueprint $table) {
            $table->id();
            $table->integer('carId');
            $table->text('meta_key');
            $table->text('meta_value');
            $table->enum('status',['available','unavailable'])->nullable()->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars_metas');
    }
};
