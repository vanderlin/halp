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

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Setup all databases.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
	

		$options = $this->option();

		// -------------------------------------
		if(is_true($options['reset'])) {
			if ($this->confirm('Do you really want to delete the tables? [yes|no]'))
			{
				$name = $this->call('migrate');
				$name = $this->call('migrate:reset');
				File::deleteDirectory(public_path('assets/content/users'));
				$this->info('--- Halp has been reset ---');
			}
			Auth::logout();
			$this->setupAll();
			return;
		}

		// -------------------------------------
		if(is_true($options['setup'])) {
			$this->setupAll();
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
	public function setupAll()
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
		
		Notification::truncate();
		Task::truncate();
		$options = $this->option();
		$task_repo = App::make('TasksRepository');

		$task_titles = ["Draw me a picture",
						"Proof-read a email",
						"Using the espresso machine",
						"Render a building",
						"Take a picture",
						"Use the 3D printer",
						"Setup a wordpress site",
						"Make a ios prototype",
						"Finding a place to eat",
						"Move a couch",
						"Chop veggies",
						"Talk about life..."];


		$durs = ['a min', 'couple of hours', 'a day', 'few mins', "10 minutes"];

		$n = isset($options['count']) ? min($options['count'], 1500) : 100;
		$faker = Faker\Factory::create();
		
		for ($i=0; $i < $n; $i++) { 
			
			$data = ['title'=>array_random_item($task_titles),
					 'project'=>Project::getRandom()->title,
					 'creator_id'=>User::getRandomID(),
					 'duration'=>array_random_item($durs)];
			
			if($faker->boolean(80))
			{
				$data['details'] = implode("\n", $faker->sentences(4));
			}

			if($faker->boolean(80))
			{
				$data['task_date'] = $faker->dateTimeBetween('now', '3 days'); 
			}

			$task = $task_repo->store($data);
			$this->info("$task->id Creating Task:$task->title");
		}

		$this->info("----- Seed Claiming -----");

		// now claime some randomly
		foreach (Task::orderByRaw("RAND()")->take(Task::count()/2)->get() as $task) {
			$task->claimed_id = User::getRandomID([$task->creator_id]);
			$task->claimed_at = $task->created_at->subDays($faker->randomDigit);
			$task->save();
			$this->info("$task->title Claimed at: ".$task->claimed_at->diffForHumans($task->created_at) );
			Event::fire(Notification::NOTIFICATION_TASK_CLAIMED, array(['task'=>$task, 'name'=>Notification::NOTIFICATION_TASK_CLAIMED])); 
		}

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
		foreach (User::all() as $user) {
			$user->delete();		
		}
	
		$faker = Faker\Factory::create();
		$seeder = new LOFaker;
		$n = 30;

		// also creat admin users (kim & I)
		$admins = array(['username'=>'tvanderlin', 'email'=>'tvanderlin@ideo.com'],
						['username'=>'kmiller', 'email'=>'kmiller@ideo.com']);
		foreach ($admins as $data) {
			$data = (object)$data;

			$user 			  			 = new User;
			$user->timestamps 		     = false;
		    $user->email 	  			 = $data->email;
		    $user->username   			 = $data->username;
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
			$photo_n = rand()%40;
			$user_photo_url = "http://api.randomuser.me/portraits/$gender/$photo_n.jpg";
			
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
			$userImage->fromSeed = true;
			$userImage->saveRemoteAsset($user_photo_url,  $user->username.".jpg", Asset::ASSET_TYPE_IMAGE);
			$userImage->save();

			$user->profileImage()->save($userImage);
			$user->profileImage->user()->associate($user);
			
	        $user->save();
	        $user->attachRole($role);
        	$user->save();

	        $this->info($user->id.' Creating User: '.$user->getName()." [$user->username, $user->email]");
			// $this->info("\t$user_photo_url");

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
