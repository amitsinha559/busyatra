<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingHistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_histories', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->integer('buses_id')->unsigned();
			$table->foreign('buses_id')->references('id')->on('buses');
			$table->string('email');
                        $table->string('phone_number');
			$table->string('booking_code');
			$table->string('from');
			$table->string('to');
			$table->string('price');
			$table->string('date_of_journey');
			$table->integer('arrival_time');
			$table->integer('journey_time');
			$table->integer('seat_numbers');
			$table->string('coupon_code')->nullable();
			$table->string('price_after_coupon_code')->nullable();
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
		Schema::drop('booking_histories');
	}

}
