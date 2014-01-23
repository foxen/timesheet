<?php

use Illuminate\Database\Migrations\Migration;

class AlterAll extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('staff', function($table){
			$table->primary('id');
		});

		Schema::table('events', function($table){
			$table->primary('id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}