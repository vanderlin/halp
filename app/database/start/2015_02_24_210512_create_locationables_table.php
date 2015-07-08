<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLocationablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('locationables')) return;
		Schema::create('locationables', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('location_id');
			$table->integer('locationable_id');
			$table->string('locationable_type');
			$table->integer('order');
			$table->integer('user_id');
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
		Schema::drop('locationables');
	}

}
