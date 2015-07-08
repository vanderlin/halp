<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePostsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {

		if(Schema::hasTable('post_types') == false) {
			Schema::create('post_types', function(Blueprint $table) {
				$table->increments('id');
				$table->timestamps();
				$table->string('name');
			});
		}

		if(Schema::hasTable('posts') == false) {
			Schema::create('posts', function(Blueprint $table) {
				
				$table->increments('id');
				$table->string('title');
				$table->string('slug');
				$table->string('hero_url')->nullable()->default(NULL);
				$table->text('body');
				$table->text('excerpt')->nullable()->default(NULL);
				

				$table->integer('user_id');
				$table->integer('post_type_id');

				$table->string('status');
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
	public function down() {
		Schema::drop('posts');
		Schema::drop('post_types');
	}

}
