<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration {

	public $prefix = 'wiwo_';

	// ------------------------------------------------------------------------
	public function up()
	{
		
		$prefix = $this->prefix;

		if(Schema::hasTable('roles') == false) 
		{
			Schema::create('roles', function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('name')->unique('roles_name_unique');
				$table->string('display_name');
				$table->timestamps();
			});
		}

		if(Schema::hasTable('assigned_roles') == false) 
		{
			Schema::create('assigned_roles', function(Blueprint $table) use($prefix)
			{
				$table->increments('id');
				$table->integer('user_id')->unsigned()->index($prefix.'assigned_roles_user_id_foreign');
				$table->integer('role_id')->unsigned()->index($prefix.'assigned_roles_role_id_foreign');
				$table->timestamps();
			});
		}
		
		if(Schema::hasTable('permissions') == false) 
		{
			Schema::create('permissions', function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('name')->unique('permissions_name_unique');
				$table->string('display_name');
				$table->timestamps();
			});
		}

		if(Schema::hasTable('permission_role') == false) 
		{
			Schema::create('permission_role', function(Blueprint $table) use($prefix)
			{
				$table->increments('id');
				$table->integer('permission_id')->unsigned()->index($prefix.'permission_role_permission_id_foreign');
				$table->integer('role_id')->unsigned()->index($prefix.'permission_role_role_id_foreign');
				$table->timestamps();
			});
		}


		if(Schema::hasTable('users') == false) 
		{
			Schema::create('users', function(Blueprint $table)
			{
				$table->increments('id');
				$table->string('username')->unique('users_username_unique');
				$table->string('email')->unique('users_email_unique');
				$table->string('password');
				$table->string('confirmation_code');
				$table->string('firstname');
				$table->string('lastname');
				$table->string('remember_token')->nullable();
				$table->boolean('confirmed')->default(0);
				$table->string('google_id');
				$table->string('google_token');
				$table->boolean('notifications')->default(1);
				$table->timestamps();
			});
		}

		if(Schema::hasTable('password_reminders') == false) {
			Schema::create('password_reminders', function(Blueprint $table)
			{
				$table->string('email');
				$table->string('token');
				$table->dateTime('created_at')->default('0000-00-00 00:00:00');
			});
		}



		// foreign keys
		if(Schema::hasTable('assigned_roles') == true) 
		{
			Schema::table('assigned_roles', function(Blueprint $table) use($prefix)
			{
				$table->foreign('role_id', $prefix.'assigned_roles_role_id_foreign')->references('id')->on('roles')->onUpdate('RESTRICT')->onDelete('RESTRICT');
				$table->foreign('user_id', $prefix.'assigned_roles_user_id_foreign')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
			});
		}

		if(Schema::hasTable('permission_role') == true) {
			Schema::table('permission_role', function(Blueprint $table) use($prefix)
			{
				$table->foreign('permission_id', $prefix.'permission_role_permission_id_foreign')->references('id')->on('permissions')->onUpdate('RESTRICT')->onDelete('RESTRICT');
				$table->foreign('role_id', $prefix.'permission_role_role_id_foreign')->references('id')->on('roles')->onUpdate('RESTRICT')->onDelete('RESTRICT');
			});
		}



		
	}


	// ------------------------------------------------------------------------
	public function down()
	{	

		$prefix = $this->prefix;
		
		Schema::table('assigned_roles', function(Blueprint $table) use($prefix)
		{
			$table->dropForeign($prefix.'assigned_roles_role_id_foreign');
			$table->dropForeign($prefix.'assigned_roles_user_id_foreign');
		});

		Schema::table('permission_role', function(Blueprint $table) use($prefix)
		{
			$table->dropForeign($prefix.'permission_role_permission_id_foreign');
			$table->dropForeign($prefix.'permission_role_role_id_foreign');
		});

		Schema::drop('users');
		Schema::drop('roles');
		Schema::drop('permissions');
		Schema::drop('password_reminders');
		
		Schema::drop('assigned_roles');
		Schema::drop('permission_role');

		
	}

}
