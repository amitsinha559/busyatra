<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('buses', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('bus_owners_id')->unsigned();
			$table->foreign('bus_owners_id')->references('id')->on('bus_owners')->onDelete('cascade');
			$table->string('bus_name');
			$table->string('bus_from');
			$table->string('bus_to');
			$table->string('bus_type');
			$table->string('bus_comfort');
			$table->string('bus_seat_image_location');
			$table->integer('bus_total_seats');
			$table->string('online_booking_seats')->nullable();
			$table->string('is_full_bus');
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
		Schema::drop('buses');
	}

}
