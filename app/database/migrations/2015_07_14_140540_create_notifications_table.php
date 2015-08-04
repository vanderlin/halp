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
			$table->integer('object_id');
			$table->string('object_type');

			$table->integer('task_id')->nullable()->default(NULL);
			$table->integer('user_id')->nullable()->default(NULL);
			$table->integer('award_id')->nullable()->default(NULL);


			$table->string('event');
			$table->timestamp('sent_at')->nullable()->default(NULL);
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
