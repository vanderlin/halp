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
		if(Schema::hasTable('assets')) return;
		Schema::create('assets', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('path');
			$table->string('name');
			$table->string('uid');
			$table->string('filename');
			$table->string('org_filename');
			$table->integer('assetable_id');
			$table->string('assetable_type');

			$table->integer('shared')->default(0)->nullable();
			$table->string('tag');

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
		Schema::drop('assets');
	}

}
