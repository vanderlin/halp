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
			$tasks = Task::unClaimed()->get()->filter(function($task) {
				if($task->notifications()->forEvent(Notification::NOTIFICATION_TASK_EXPIRED)->get()->count() == 0 && $task->isExpired())
				{
					return $task;
				}

			});
			foreach ($tasks as $task) {
				$ago = $task->date->diffForHumans();
				$this->info("($task->id) $task->title Expired - $ago");
				$n = $task->notifications()->forEvent(Notification::NOTIFICATION_TASK_EXPIRED)->get()->count();
				if($n == 0) {
					Notification::fire($task, Notification::NOTIFICATION_TASK_EXPIRED);	
					$this->info("\tNotification Created ".$task->id);
				}
				else {
					$this->info("*** Notification not sent");
				}
			}
			if($tasks->count() == 0) {
				$this->info("*** No expired tasks found ***");
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
