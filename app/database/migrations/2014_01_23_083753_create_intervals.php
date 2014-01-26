<?php

use Illuminate\Database\Migrations\Migration;

class CreateIntervals extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('intervals', function($table){
			$table->integer('graf_id');
			$table->integer('int_id');
			$table->integer('day_number');
			$table->time('time_begin')->default('09:00');
			$table->time('time_end')->default('18:00');
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
		Schema::drop('intervals');
	}

}