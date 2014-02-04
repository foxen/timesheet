<?php

use Illuminate\Database\Migrations\Migration;

class AlterParsed extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$query = "alter table parsed modify hours int";
		\DB::statement($query);

		$query = "alter table parsed modify delay int";
		\DB::statement($query);
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