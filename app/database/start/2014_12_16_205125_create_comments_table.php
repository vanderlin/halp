<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCommentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('comments')) return;
		Schema::create('comments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('body');
			$table->integer('user_id');
			$table->string('commentable_type');
			$table->integer('commentable_id');
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
		Schema::drop('comments');
	}

}
