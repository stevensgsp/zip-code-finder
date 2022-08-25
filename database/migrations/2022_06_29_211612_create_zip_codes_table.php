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
        Schema::create('zip_codes', function (Blueprint $table) {
            $table->id();

            $table->string('zip_code');
            $table->json('locality')->nullable();
            $table->json('federal_entity')->nullable();
            $table->json('municipality')->nullable();
            $table->json('settlements')->nullable();

            $table->timestamps();

            $table->unique('zip_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('zip_codes');
    }
};
