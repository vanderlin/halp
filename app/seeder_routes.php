<?php 

use Task\Task;

// ------------------------------------------------------------------------
Route::get('seeder/halp', function() {

	// make some fake users
	if(User::all()->count() < 2) {
		$seeder = new LOFaker;
		$n = 4;
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
	$durs = ['a min', 'couple of hours', 'a day', 'few mins'];
	for ($i=0; $i < $n; $i++) { 
		
		$data = [
			'title'=>$faker->text(20),
			'creator_id'=>User::getRandomID(),
			'claimed_id'=>rand()%10>5?User::getRandomID():null,
			'project_id'=>Project\Project::getRandomID(),
			'duration'=>$durs[array_rand($durs)]
			];
		$task = new Task($data);
		$task->save();
		array_push($tasks, $task);
	}
	return $tasks;
});

// ------------------------------------------------------------------------
Route::get('seeder/users', function() {
	$seeder = new LOFaker;
	return $seeder->createFakeUser();
});
