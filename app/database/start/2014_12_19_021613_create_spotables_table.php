<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpotablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('spotables')) return;
		Schema::create('spotables', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('spot_id');
			$table->integer('spotable_id');
			$table->integer('order');
			$table->string('spotable_type');
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
		Schema::drop('spotables');
	}

}
