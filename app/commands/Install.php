<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Install extends Command {

	// ------------------------------------------------------------------------
	protected $name = 'halp:install {reset}';

	// ------------------------------------------------------------------------
	protected $description = 'Install Halp';

	// ------------------------------------------------------------------------
	public function __construct()
	{
		parent::__construct();
	}

	// ------------------------------------------------------------------------
	public function fire()
	{
		if ($this->confirm('Do you want to install all databases [y|N]')) {
			$this->call('halp:databases');
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}
