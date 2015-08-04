<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Award\AwardsRepository;
use Carbon\Carbon;

class AwardsCommand extends Command {

	
	protected $name = 'halp:awards';
	protected $repository;
	protected $description = 'find awards for users';


	public function __construct()
	{
	
		parent::__construct();
	}

	// ------------------------------------------------------------------------
	public function errorResponse($message) 
	{
		if(is_string($message)) {
			$this->comment($message);	
		}
		else {
			$this->comment(json_encode($message, JSON_PRETTY_PRINT));	
		}
		
	}
	
	// ------------------------------------------------------------------------
	public function statusResponse($message, $status=200, $withInput=true) 
	{
		if(is_string($message)) {
			$this->info($message);	
		}
		else {
			$this->info(json_encode($message, JSON_PRETTY_PRINT));	
		}
	}
	
	// ------------------------------------------------------------------------
	public function fire()
	{

		$options = $this->option();	
		$users = User::all();

		
		$this->repository = new AwardsRepository;
		$this->repository->setListener($this);


		$this->comment("// -------------------------------------");
		$this->comment("           Individual Awards            ");
		$this->comment("// -------------------------------------");
		// user based awards
		foreach ($users as $user) {
			$this->info("Checking awards for: ".$user->getName());
			$this->info($this->repository->checkAwardForUser($user));
		}
		$this->comment("// -------------------------------------");
		$this->comment("             Site Wide Awards           ");
		$this->comment("// -------------------------------------");
		// site wide awards - time based
		
		if(is_true($options['full'])) {
			$start = Task::orderBy('created_at')->first()->created_at;
			$end   = Carbon::now();
			$date = clone $start;
			for ($i=$start->weekOfYear; $i<=$end->weekOfYear; $i++) {
				$this->info("Checking for week of ".$date->toDateString());
				$this->info($this->repository->checkForAwards($date));
				$date->addWeek();
			}
		}
		else {
			$this->info($this->repository->checkForAwards());
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
			array('full', null, InputOption::VALUE_OPTIONAL, 'run from the begining of time...', null),
		);
	}

}
