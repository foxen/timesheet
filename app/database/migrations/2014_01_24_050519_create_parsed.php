<?php

use Illuminate\Database\Migrations\Migration;

class CreateParsed extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('parsed', function($table){
			
			$table->integer('in_id')                             ->primary();
			$table->integer('out_id')        	  ->nullable();
			$table->integer('staff_id')      	  ->nullable()   ->index();
			$table->string('subdiv', 150)    	  ->nullable();
			$table->string('appoint', 150)   	  ->nullable();
			$table->string('name', 150)      	  ->nullable();
			$table->integer('graf_id')       	  ->nullable();
			$table->dateTime('in_datetime')  	  ->nullable()   ->index();
			$table->dateTime('out_datetime') 	  ->nullable()   ->index();
			$table->dateTime('prev_out_datetime') ->nullable();
			$table->date('dt')                    ->nullable();
			$table->time('hours')                 ->nullable();
			$table->integer('dow')                ->nullable();
			$table->time('gr_in')                 ->nullable();
			$table->time('delay')                 ->nullable();
			$table->integer('int_id')             ->nullable();
			
			$table->timestamps();

		});

		Schema::create('tmp_parsed', function($table){
			
			$table->integer('in_id')                             ->primary();
			$table->integer('out_id')        	  ->nullable();
			$table->integer('staff_id')      	  ->nullable()   ->index();
			$table->string('subdiv', 150)    	  ->nullable();
			$table->string('appoint', 150)   	  ->nullable();
			$table->string('name', 150)      	  ->nullable();
			$table->integer('graf_id')       	  ->nullable();
			$table->dateTime('in_datetime')  	  ->nullable()   ->index();
			$table->dateTime('out_datetime') 	  ->nullable()   ->index();
			$table->dateTime('prev_out_datetime') ->nullable();
			$table->date('dt')                    ->nullable();
			$table->time('hours')                 ->nullable();
			$table->integer('dow')                ->nullable();
			$table->time('gr_in')                 ->nullable();
			$table->time('delay')                 ->nullable();
			$table->integer('int_id')             ->nullable();
			
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