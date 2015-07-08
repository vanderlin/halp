<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() 
	{

		if(Schema::hasTable('activities')) return;
	
		Schema::create('activities', function(Blueprint $table) {
			$table->increments('id');
			$table->string('event');
			$table->string('activity_type');
			$table->integer('activity_id');
			$table->integer('user_id');
			$table->integer('asset_id');
			$table->softDeletes();
			$table->timestamps();
		});
	
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('activities');
		
	}

}
