<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusDeparturePointsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bus_departure_points', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('buses_id')->unsigned();
			$table->foreign('buses_id')->references('id')->on('buses')->onDelete('cascade');
                        $table->string('departure_from');
                        $table->string('departure_time');
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
		Schema::drop('bus_departure_points');
	}

}
