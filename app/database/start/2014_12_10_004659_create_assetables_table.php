<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssetablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if(Schema::hasTable('assetables')) return;
		Schema::create('assetables', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('assetable_id');
			$table->string('assetable_type');
			$table->integer('asset_id');
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
		Schema::drop('assetables');
	}

}
