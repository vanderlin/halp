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
	protected $name = 'site:setup';

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
		if(is_true($options['reset'])) {
			if ($this->confirm('Do you really want to delete the tables? [yes|no]'))
			{
				$name = $this->call('migrate:reset');
				File::deleteDirectory(public_path('assets/content/users'));
			}
			Auth::logout();
		}

		if(Project::all()->count() == 0) {
			$project_names = ["Bravo", "Tinker", "Pando", "Denso", "Rabbit", "Personal", "IDEO", "Groover", "Nutcracker"];
			foreach ($project_names as $name) {
				$prj = new Project(['title'=>$name, 'user_id'=>User::getRandomID()]);
				$prj->save();
			}
		}

		
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
			}
		}

		foreach (Role::all() as $r) {
			$this->info("Role: ".$r->name." Created");
		}

		if($options['seed']=='all') {
			$this->seed();
		}

		if($options['seed']=='users') {
			$this->seedUsers();
		}

		if($options['seed']=='tasks') {
			$this->seedTasks();
		}
		
	}

	// ------------------------------------------------------------------------
	public function seedTasks()
	{
		
		Notification::truncate();
		Task::truncate();

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

		$n = 10;
		$faker = Faker\Factory::create();
		
		for ($i=0; $i < $n; $i++) { 
			
			$data = ['title'=>array_random_item($task_titles),
					 'project'=>Project::getRandom()->title,
					 'creator_id'=>User::getRandomID(),
					 'duration'=>array_random_item($durs)];
			
			$task = $task_repo->store($data);
			$this->info("$task->id Creating Task:$task->title");
		}

		$this->info("----- Seed Claiming -----");

		// now claime some randomly
		foreach (Task::orderByRaw("RAND()")->take(Task::count()/2)->get() as $task) {
			$task->claimed_id = User::getRandomID([$task->creator_id]);
			$task->claimed_at = $task->created_at->addDays($faker->randomDigit);
			$task->save();
			$this->info("$task->title Claimed at: ".$task->claimed_at->diffForHumans($task->created_at) );
			Event::fire(Notification::NOTIFICATION_TASK_CLAIMED, array(['task'=>$task, 'name'=>Notification::NOTIFICATION_TASK_CLAIMED])); 
		}
	}

	// ------------------------------------------------------------------------
	public function seed()
	{
		$this->info('Seeding...');
		$r = get_remote_file('http://localhost:8888/seeder/halp');
		$this->info($r);

		$this->info('--- Done Seeding ---');

		return;
		// make some fake users
		if(User::all()->count() < 2) {
			$seeder = new LOFaker;
			$n = 2;
			for ($i=0; $i < $n; $i++) { 
				$seeder->createFakeUser();
			}
		}

		if(Project\Project::all()->count() < 2) {
			$project_names = ["Bravo", "Tinker", "Pando", "Denso", "Rabbit"];
			foreach ($project_names as $name) {
				$prj = new Project\Project(['title'=>$name, 'user_id'=>User::getRandomID()]);
				$prj->save();
			}
		}


		$n = 10;
		$tasks = [];
		$faker = Faker\Factory::create();
		for ($i=0; $i < $n; $i++) { 
			
			$data = [
				'title'=>$faker->text(20),
				'creator_id'=>User::getRandomID(),
				'claimed_id'=>rand()%10>5?User::getRandomID():null,
				'project_id'=>Project\Project::getRandomID()
				];
			$task = new Task($data);
			$task->save();
			array_push($tasks, $task);
		}
		return $tasks;
	}

	// ------------------------------------------------------------------------
	public function seedUsers()
	{

		foreach (User::all() as $user) {
			
			if($user->isAdmin()==false) {
				$user->delete();	
			}
		}

		$seeder = new LOFaker;
		$n = 30;
		$faker = Faker\Factory::create();

		for ($i=0; $i < $n; $i++) { 
			
			$gender = array_random_item(['men', 'women']);
			$photo_n = rand()%40;
			$user_photo_url = "http://api.randomuser.me/portraits/$gender/$photo_n.jpg";
			
			$role = $role = Role::where('name', '=', 'Writer')->first();
			$joinDate = $faker->dateTimeBetween('-3 years', 'now');
		
			$user 			  = new User;
			$user->timestamps = false;
		    $user->email 	  = 'fake_'.$faker->email;
		    $user->firstname  = $faker->firstname;
		    $user->lastname   = $faker->lastname;

		    $user->username   = strtolower($user->firstname[0].$user->lastname)."$faker->randomDigit";

			$password 		  			 = Hash::make($user->username);
			$user->password 			 = $password;
			$user->password_confirmation = $password;
			$user->confirmed 			 = 1;
			$user->confirmation_code 	 = md5($user->username.time('U'));
			$user->created_at = $user->updated_at = $joinDate;
			$user->save();

			$userImage = new Asset;
			$userImage->path = public_path('assets/content/users');
			$userImage->saveRemoteAsset($user_photo_url,  $user->username.".jpg", Asset::ASSET_TYPE_IMAGE);
			$userImage->save();

			$user->profileImage()->save($userImage);
			$user->profileImage->user()->associate($user);
			
	        $user->save();

	        $this->info('Creating User: '.$user->getName()." [$user->username, $user->email]");
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
			array('reset', null, InputOption::VALUE_OPTIONAL, 'reset database.', null),
			array('seed', null, InputOption::VALUE_OPTIONAL, 'seed database.', null),
		);
	}

}
