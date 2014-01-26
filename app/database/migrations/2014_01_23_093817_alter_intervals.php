<?php

use Illuminate\Database\Migrations\Migration;

class AlterIntervals extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('intervals', function($table){
			$table->primary(array('graf_id', 'int_id', 'day_number'));	
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