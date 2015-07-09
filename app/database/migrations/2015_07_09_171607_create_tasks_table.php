<?php

use \Task;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->integer('project_id');
			$table->integer('creator_id');
			$table->string('duration');
			$table->integer('claimed_id')->nullable()->default(NULL);
			$table->timestamp('claimed_at');
			$table->softDeletes();
			$table->timestamps();
		});

		if(Config::getEnvironment() == 'local')
		{
		
			
			
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tasks');
	}

}
