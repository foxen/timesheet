<?php

use Illuminate\Database\Migrations\Migration;

class AlterP extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$query = "ALTER TABLE `timesheet`.`tmp_parsed` CHANGE COLUMN `delay` `delay` INT NULL DEFAULT NULL";
		\DB::statement($query);

		$query = "ALTER TABLE `timesheet`.`tmp_parsed` CHANGE COLUMN `hours` `hours` INT NULL DEFAULT NULL";
		\DB::statement($query);

		$query = "ALTER TABLE `timesheet`.`parsed` CHANGE COLUMN `delay` `delay` INT NULL DEFAULT NULL";
		\DB::statement($query);

		$query = "ALTER TABLE `timesheet`.`parsed` CHANGE COLUMN `hours` `hours` INT NULL DEFAULT NULL";
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