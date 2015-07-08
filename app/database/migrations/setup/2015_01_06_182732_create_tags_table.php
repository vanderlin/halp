<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTagsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('tags') == false) 
		{
			
			Schema::create('tags', function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('name');
				$table->string('slug');
				$table->integer('user_id');
				$table->timestamps();
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
		Schema::drop('tags');
	}

}
