<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSpotsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('spots')) return;
		Schema::create('spots', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name');
			$table->string('type');
			$table->text('description');
			$table->integer('user_id');
			$table->integer('location_id');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('spots');
	}

}
