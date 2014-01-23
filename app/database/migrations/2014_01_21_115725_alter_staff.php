<?php

use Illuminate\Database\Migrations\Migration;

class AlterStaff extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('staff', function($table){
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
		Schema::table('staff', function($table){
			$table->drop('created_at');
			$table->drop('updatet_at');
		});
	}

}