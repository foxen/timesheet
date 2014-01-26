<?php

use Illuminate\Database\Migrations\Migration;

class AlterEvents2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('events', function($table){
			$table->string('area',5)->default('walk')->nulable()->after('ev_src');
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