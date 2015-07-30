<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Task\Task;
use Notification\Notification;

class CronCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'cron';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Check for new notifications';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// ------------------------------------------------------------------------
	public function fire()
	{
		$options = $this->option();	
		$debug = is_true($options['debug']);

		if($options['job'] == 'expired_tasks') {
			$this->info("Looking for expired tasks...");
			$tasks = Task::expired()
						->whereHas('notifications', function($q) {
							$q->where('event', '<>', Notification::NOTIFICATION_TASK_EXPIRED);
						})->get();
			
			foreach ($tasks as $task) {
				$ago = $task->date->diffForHumans();
				$this->info("($task->id) $task->title Expired - $ago");

				Notification::fire($task, Notification::NOTIFICATION_TASK_EXPIRED);

				$n = $task->notifications()->forEvent(Notification::NOTIFICATION_TASK_EXPIRED)->get()->count();
				$this->info("$n");
			}
			return;
		}

		if($options['job'] == 'notifications') {

			// first get all users that want to receive notifications
			$users = User::where('notifications', '=', 1)->get();

			// get all notifications that have not been sent out
			$notifications = Notification::whereNull('sent_at')->get();
			if($notifications->count()==0) 
			{
				$this->info("*** No New Notification ***");
				return;
			}

			$results = [];
			foreach ($notifications as $notice) {
				$this->info("Notification: ".$notice->getTitle()." : ".$notice->event);
				$status = $notice->send($debug);
				$this->info("\t status: ".strbool($status));
			}
			return  $results;
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
			array('job', null, InputOption::VALUE_OPTIONAL, 'what cron job to run?', null),
			array('debug', null, InputOption::VALUE_OPTIONAL, 'run in debug mode', null),
		);
	}

}
