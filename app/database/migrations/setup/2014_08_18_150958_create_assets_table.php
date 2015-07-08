<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('assets') == false) 
		{
			Schema::create('assets', function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('path');
				$table->string('name');
				$table->integer('user_id')->unsigned();
				$table->string('uid');
				$table->string('filename');
				$table->string('type');
				$table->string('org_filename');
				$table->integer('assetable_id')->nullable()->default(NULL);
				$table->string('assetable_type')->nullable()->default(NULL);
				$table->string('source');

				$table->integer('shared')->default(0)->nullable();
				$table->string('tag');
				$table->softDeletes();
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
		Schema::drop('assets');
	}

}
