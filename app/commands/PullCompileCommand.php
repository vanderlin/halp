<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;


class PullCompileCommand extends Command {

	
	protected $name = 'halp:build';

	
	protected $description = 'pull and build';


	public function __construct()
	{
		parent::__construct();
	}

	// ------------------------------------------------------------------------
	public function runCmd($cmd)
	{
		$output = "";
		exec($cmd, $output);
		foreach ($output as $value) {
			$this->info($value);
		}	
		return $output;
	}
	// ------------------------------------------------------------------------
	public function fire()
	{
		$this->runCmd('git pull');
		$this->runCmd('grunt build');
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
		);
	}

}
