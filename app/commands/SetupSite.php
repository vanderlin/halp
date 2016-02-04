<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Task\Task;
use Project\Project;
use Notification\Notification;

class SetupSite extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'halp';
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

   		
		// -------------------------------------
		if(is_true($options['reset'])) {

			if(Config::getEnvironment() == 'production') {
				$really = $this->confirm('This is the *** PRODUCTION *** server are you sure!? [yes|no]');
				if(!$really) {
					$this->info("**** Exiting ****");
					exit();
				}
			}
			
	   		if (!File::exists($this->seed_path)) {
	   			File::makeDirectory($this->seed_path);
	   		
		   		$n = 50;
		   		for ($i=1; $i <= $n; $i++) { 
		   			$gender_types = ['men', 'women'];
					foreach ($gender_types as $gender) {
						$user_photo_url = "http://api.randomuser.me/portraits/$gender/$i.jpg";
						File::put($this->seed_path."/{$gender}_{$i}.jpg", file_get_contents($user_photo_url));
					}
					$this->info("Cache user seed image - $i");
		   		}
			}

			if ($this->confirm('Do you really want to delete the tables? [yes|no]'))
			{

				// first delete all assets
				if(Schema::hasTable('assets')) {
					foreach (Asset::all() as $asset) {
						$asset->delete();
					}
				}
				
				$name = $this->call('migrate');
				$name = $this->call('migrate:reset');
				File::deleteDirectory(public_path('assets/content/users'));
				$this->info('--- Halp has been reset ---');
			}
			Auth::logout();
			$this->setupDatabases();
			return;
		}

		// -------------------------------------
		if(is_true($options['setup'])) {
			$this->setupDatabases();
		}
		
		// -------------------------------------
		

		if($options['seed']=='all') {
			$this->seed();
		}

		if($options['seed']=='users') {
			$this->seedUsers();
		}

		if($options['seed']=='tasks') {
			$this->seedTasks();
		}

		if($options['seed']=='projects') {
			$this->seedProjects();
		}
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

		$this->seed();

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

		$this->comment("****\tHalp has been setup :-) \t****");
		return;
	}

	// ------------------------------------------------------------------------
	public function seedProjects()
	{
		if(Project::all()->count() == 0) {
			$project_names = ["Bravo", "Tinker", "Pando", "Denso", "Rabbit", "Personal", "IDEO", "Groover", "Nutcracker"];
			foreach ($project_names as $name) {
				$prj = new Project(['title'=>$name, 'user_id'=>User::getRandomID()]);
				$prj->save();
				$this->info("$prj->id Creating Project:$prj->title");
			}
		}
	}

	// ------------------------------------------------------------------------
	public function seedTasks()
	{
		
		foreach (Task::all() as $task) {
			$task->delete();
		}
		Task::truncate();

		Notification::truncate();

		$options = $this->option();
		$task_repo = App::make('TasksRepository');

		$task_titles = ["Draw me a picture",
						"Proof-read a email",
						"Using the espresso machine",
						"Render a building",
						"Take a picture",
						"Make a latte",
						"Sing a song",
						"Giving a hug",
						"Use the 3D printer",
						"Setup a wordpress site",
						"Make a ios prototype",
						"Finding a place to eat",
						"Move a couch",
						"Chop veggies",
						"Talk about life..."];


		$durs = ['a min', 'couple of hours', 'a day', 'few mins', "2 minutes", "an hour", "1/2 hour", "5 minutes", "20 minutes", "10 minutes"];

		$n = isset($options['count']) ? min($options['count'], 1500) : 250;
		$faker = Faker\Factory::create();
		
		for ($i=0; $i < $n; $i++) { 
			
			$created_at = $faker->dateTimeBetween('-3 days', '3 days');
			if($faker->boolean(80)) {
				$created_at = $faker->dateTimeBetween('-3 months', '3 months');
			}
			
			$data = ['title'=>array_random_item($task_titles),
					 'project'=>Project::getRandom()->title,
					 'creator_id'=>User::getRandomID(),
					 'duration'=>array_random_item($durs),
					 'created_at'=>$created_at
					 ];

			// dd($data);
			if($faker->boolean(80))
			{
				$data['details'] = implode("\n", $faker->sentences(4));
			}

			if($faker->boolean(10))
			{
				$data['does_not_expire'] = true;
				$data['task_date'] = NULL;
			}

			$task = $task_repo->store($data);
			
			$this->info("$task->id Creating Task:$task->title");
		}

		$this->comment("----- Seed Claiming -----");

		// Get a bunch for a few users
		for ($i = 0; $i <= User::count()/2; $i++) {
			$user_id = User::getRandomID();
			for ($j = 0; $j <= $faker->numberBetween(5, 120); $j++) {
				$task = Task::orderByRaw("RAND()")->where('creator_id', '<>', $user_id)->take(1)->first();
				$task->claimed_id = $user_id;
				$task->claimed_at = $task->date->subDays($faker->randomDigit);
				$task->save();
				$this->info("$task->title Claimed at: ".$task->claimed_at->diffForHumans($task->created_at) );
				Notification::fire($task, Notification::NOTIFICATION_TASK_CLAIMED); 
			}
		}

		// now claime some randomly
		foreach (Task::orderByRaw("RAND()")->take(Task::count()/2)->get() as $task) {
			$task->claimed_id = User::getRandomID([$task->creator_id]);
			$task->claimed_at = $task->date->subDays($faker->randomDigit);
			$task->save();
			$this->info("$task->title Claimed at: ".$task->claimed_at->diffForHumans($task->created_at) );
			Notification::fire($task, Notification::NOTIFICATION_TASK_CLAIMED); 
		}


		$this->call('halp:awards', array('--full' => 'true'));
		


	}

	// ------------------------------------------------------------------------
	public function seed()
	{

		$this->comment("------- Seeding Users ------- ");
		$this->seedUsers();

		$this->comment("------- Seeding Projects ------- ");
		$this->seedProjects();
		
		$this->comment("------- Seeding Tasks ------- ");
		$this->seedTasks();
	
	}

	// ------------------------------------------------------------------------
	public function seedUsers()
	{

		$user_photos = File::files($this->seed_path);
			
		Asset::setFromSeed(true);
		foreach (User::all() as $user) {
			$user->delete();		
		}
		
		$faker = Faker\Factory::create();
		$seeder = new LOFaker;
		$n = 50;

		// also creat admin users (kim & I)
		$admins = array(['username'=>'tvanderlin', 'firstname'=>'Todd', 'lastname'=>'Vanderlin', 'email'=>'tvanderlin@ideo.com'],
						['username'=>'kmiller', 'firstname'=>'Kim', 'lastname'=>'Miller', 'email'=>'kmiller@ideo.com']);
		foreach ($admins as $data) {
			$data = (object)$data;

			$user 			  			 = new User;
			$user->timestamps 		     = false;
		    $user->email 	  			 = $data->email;
		    $user->username   			 = $data->username;
		    $user->firstname  			 = $data->firstname;
		    $user->lastname              = $data->lastname;
			$password 		  			 = Hash::make($user->username);

			$user->password 			 = $password;
			$user->password_confirmation = $password;
			$user->confirmed 			 = 1;
			$user->confirmation_code 	 = md5($user->username.time('U'));
			$user->created_at 			 = $user->updated_at = $faker->dateTimeBetween('-3 years', 'now');
	        $user->save();

			$role = Role::where('name', '=', 'Admin')->first();

 		    $user->save();
	        $user->attachRole($role);
        	$user->save();

	        $this->info('Creating *** Admin *** User: '.$user->getName()." [$user->username, $user->email]");
		}
		
		$this->info("\t");
		
		for ($i=0; $i < $n; $i++) { 
			
			$gender = array_random_item(['men', 'women']);
			$photo  = array_random_item($user_photos);

			
			$role = Role::where('name', '=', 'Writer')->first();
			$joinDate = $faker->dateTimeBetween('-3 years', 'now');
		
			$user 			  = new User;
			$user->timestamps = false;
		    $user->email 	  = 'fake_'.$faker->unique()->email;
		    $user->firstname  = $faker->firstname;
		    $user->lastname   = $faker->lastname;

		    $user->username   = preg_replace("/[^A-Za-z0-9 ]/", '', $faker->unique()->userName);

			$password 		  			 = Hash::make($faker->password);
			$user->password 			 = $password;
			$user->password_confirmation = $password;
			$user->confirmed 			 = 1;
			$user->confirmation_code 	 = md5($user->username.time('U'));
			$user->created_at = $user->updated_at = $joinDate;
			
			if($user->save() == false) {
				$this->error($user->errors()." ".$user->username);
			}

			$userImage = new Asset;
			$userImage->path = 'assets/content/users';
			$userImage->saveLocalFile($photo, $user->username.".jpg", Asset::ASSET_TYPE_IMAGE);
			$userImage->save();

			$user->profileImage()->save($userImage);
			$user->profileImage->user()->associate($user);
			
	        $user->save();
	        $user->attachRole($role);
        	$user->save();

	        $this->info($user->id.' Creating User: '.$user->getName()." [$user->username, $user->email]");

		}

		foreach (User::all() as $user) {
			Notification::fire($user, Notification::NOTIFICATION_HALP_WELCOME); 
		}

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
