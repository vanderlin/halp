<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('locations')) return;
		Schema::create('locations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->float('lng');
			$table->float('lat');
			$table->string('name');
			$table->string('place_id');
			$table->timestamps();
			$table->integer('user_id');
			$table->integer('spot_id');
			$table->integer('locationable_id');
			$table->string('locationable_type')->default('');
			$table->text('details');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('locations');
	}

}
