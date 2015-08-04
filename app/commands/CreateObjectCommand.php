<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


class CreateObjectCommand extends Command {

	
	protected $name = 'generate:object';
	protected $description = 'boiler-plate for a new object';


	public function __construct()
	{
		parent::__construct();
	}

	// ------------------------------------------------------------------------
	public function fire()
	{
		$options = $this->option();	
		

		if(isset($options['name'])) {
			$this->info("Create new object {$options['name']}");
			$name = $options['name'];
			$path = app_path('providers/'.$name);
			$this->info("creating... {$path}");
			if(File::exists($path)) {
				$this->comment("*** $path already exist ***");
				return;
			}
			else {
				File::makeDirectory($path);
				
				$files = array(	"{$name}.php",
								"{$name}sObserver.php",
								"{$name}sRepository.php",
								"{$name}sServiceProvider.php");
				foreach ($files as $file) {
					$status = File::put($path.'/'.$file, "");
					$this->info("{$file} ($status)");
				}
				$this->info("Done...");
			}
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
			array('name', null, InputOption::VALUE_OPTIONAL, 'object name', null),
		);
	}

}
