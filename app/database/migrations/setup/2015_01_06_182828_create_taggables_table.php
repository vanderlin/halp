<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTaggablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('taggables')) 
		{
			Schema::create('taggables', function(Blueprint $table)
			{
				$table->increments('id');
				$table->integer('taggable_id');
				$table->string('taggable_type');
				$table->integer('tag_id');
			});
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('taggables');
	}

}
