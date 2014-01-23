<?php

use Illuminate\Database\Migrations\Migration;

class CreateStaff extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('staff', function($table){
			$table->integer('id');
			$table->string('name', 150);
			$table->string('subdiv', 150);
			$table->string('appoint', 150);
			$table->integer('graf_id')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('staff');
	}

}