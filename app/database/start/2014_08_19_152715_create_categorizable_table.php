<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCategorizableTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('categorizables')) return;
		Schema::create('categorizables', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('categorizable_id')->unsigned();
			$table->string('categorizable_type');
			$table->integer('category_id')->unsigned();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::drop('categorizables');
	}

}
