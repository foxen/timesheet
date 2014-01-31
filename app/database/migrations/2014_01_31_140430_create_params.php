<?php

use Illuminate\Database\Migrations\Migration;

class CreateParams extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('params', function($table){
			$table->string('param', 150)->primary();
			$table->string('val', 150)->nullable();
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
		//
	}

}