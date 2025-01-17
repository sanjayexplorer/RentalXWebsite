<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_images', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('url');
            $table->text('base_url');
            $table->text('withoutPublicUrl')->nullable();
            $table->text('ImageUniqueId')->nullable();
            $table->enum('status',['active','inactive'])->nullable()->default('active');
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
        Schema::dropIfExists('master_images');
    }
};
