<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('notifications', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('task_id');
			$table->string('event');
			$table->timestamp('sent_on')->nullable()->default(NULL);
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
		Schema::drop('notifications');
	}

}
