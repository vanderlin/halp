<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Task\Task;
use Project\Project;
use Notification\Notification;

class CreateDatabases extends Command {

	protected $name = 'halp:databases';
	protected $seed_path;
	protected $description = 'Setup all Halp databases.';

	// ------------------------------------------------------------------------
	public function __construct()
	{
		parent::__construct();
	}

	// ------------------------------------------------------------------------
	public function fire()
	{
		$options = $this->option();		
		$this->seed_path = storage_path('seeder');
		Asset::setFromSeed(true);
		$this->setupDatabases();
	}

	// ------------------------------------------------------------------------
	public function setupDatabases()
	{

		$name = $this->call('migrate', array('--path'=>'app/database/migrations/setup/'));
		$name = $this->call('migrate');

		// create the roles
		$roles = ['Admin', 'Writer', 'Reader'];
		foreach ($roles as $r) {
			$role = Role::whereName($r)->first();
			if($role == null) {
				$role = new Role;
				$role->name = $r;
				$role->display_name = $r;
				$role->save();
				$this->info("$role->id Creating Role:$r");
			}
		}
		foreach (User::all() as $u) {
			$this->info("$u->id : user: $u->username");
		}

		// add core assets
        $m = Asset::findFromTag('missing-user-image');
        if($m == NULL)
        {	
        	$m = new Asset();
        	$m->path = "assets/content/uploads";
        	$m->saveLocalFile(public_path('assets/content/common/missing/profile-default.png'), 'profile-default.png');
			$m->tag = 'missing-user-image';
			$m->shared = 1;
			$m->type = Asset::ASSET_TYPE_IMAGE;
			$m->save();
        }

		$this->comment("****\tAll Databases for Halp have been setup :-) \t****");
		return;
	}

	// ------------------------------------------------------------------------
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	// ------------------------------------------------------------------------
	protected function getOptions()
	{
		return array(
			array('setup', null, InputOption::VALUE_OPTIONAL, 'setup the site and migrate all tables', null),
			array('reset', null, InputOption::VALUE_OPTIONAL, 'reset database.', null),
			array('count', null, InputOption::VALUE_OPTIONAL, 'how many?', null),
			array('seed', null, InputOption::VALUE_OPTIONAL, 'seed database.', null),
		);
	}

}
