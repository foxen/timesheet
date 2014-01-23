<?php

use Illuminate\Database\Migrations\Migration;

class CreateEvents extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('events', function($table){
			$table->integer('id')->index();
			$table->date('dt');
			$table->time('tm');
			$table->dateTime('dt_tm')->index()->nullable();
			$table->integer('staff_id')->index();
			$table->integer('ev_src')->index();
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
		Schema::drop('events');
	}

}