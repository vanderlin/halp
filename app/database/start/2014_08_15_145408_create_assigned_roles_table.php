<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAssignedRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		
		if(Schema::hasTable('assigned_roles')) return;
		
		Schema::create('assigned_roles', function(Blueprint $table) {
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index('assigned_roles_user_id_foreign');
			$table->integer('role_id')->unsigned()->index('assigned_roles_role_id_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('assigned_roles');
	}

}
