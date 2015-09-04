<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusDroppingPointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bus_dropping_points', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->integer('buses_id')->unsigned();
			$table->foreign('buses_id')->references('id')->on('buses')->onDelete('cascade');
                        $table->integer('bus_departure_points_id')->unsigned();
			$table->foreign('bus_departure_points_id')->references('id')->on('bus_departure_points')->onDelete('cascade');
                        $table->string('dropping_point');
                        $table->string('dropping_time');
                        $table->string('price');
                        $table->string('extra_price_one')->nullable();
                        $table->string('extra_price_two')->nullable();
                        $table->string('reduce_price')->nullable();
                        $table->string('percentage_increament_price')->nullable();
                        $table->string('percentage_reduction_price')->nullable();
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
		Schema::drop('bus_dropping_points');
	}

}
