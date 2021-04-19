<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStolenCars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stolen_cars', function (Blueprint $table) {
            $table->id();
            $table->string('car_number');
            $table->string('full_name');
            $table->string('color');
            $table->string('vin', 17);
            $table->unsignedBigInteger('make_id')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->integer('year')->index('car_stolen_year')->nullable();
            $table->timestamps();

            $table->foreign('make_id')->references('id')->on('car_makes');
            $table->foreign('model_id')->references('id')->on('car_models');
        });

        DB::statement('ALTER TABLE stolen_cars ADD FULLTEXT search(full_name, car_number, vin)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stolen_cars');
    }
}
