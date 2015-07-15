<?php
/*2014_08_15_145408_create_assigned_roles_table.php
2014_08_15_145408_create_categories_table.php
2014_08_15_145408_create_locations_table.php
2014_08_15_145408_create_password_reminders_table.php
2014_08_15_145408_create_permission_role_table.php
2014_08_15_145408_create_permissions_table.php
2014_08_15_145408_create_roles_table.php
2014_08_15_145408_create_users_table.php
2014_08_15_145409_add_foreign_keys_to_assigned_roles_table.php
2014_08_15_145409_add_foreign_keys_to_permission_role_table.php
2014_08_18_150958_create_assets_table.php
2014_08_19_152715_create_categorizable_table.php
2014_12_04_163029_create_spots_table.php
2014_12_10_004659_create_assetables_table.php
2014_12_11_194153_create_offices_table.php
2014_12_16_205125_create_comments_table.php
2014_12_18_165627_create_itineraries_table.php
2014_12_19_021613_create_spotables_table.php
2014_12_22_163319_create_faqs_table.php
2014_12_30_160547_create_userable_table.php
2015_01_06_182732_create_tags_table.php
2015_01_06_182828_create_taggables_table.php
2015_01_08_182954_create_visits_table.php
2015_01_29_202529_create_activities_table.php
2015_02_09_154251_create_posts_table.php
2015_02_24_210512_create_locationables_table.php*/
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
	protected $name = 'cron:notifications';

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

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
	
		Notification::checkForNotifications();	
	}

	// ------------------------------------------------------------------------
	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			// array('example', InputArgument::REQUIRED, 'An example argument.'),
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
		);
	}

}
