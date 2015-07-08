<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserableTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		if(Schema::hasTable('userables')) return;
		Schema::create('userables', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('userable_id');
			$table->string('userable_type');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('userables');
	}

}
