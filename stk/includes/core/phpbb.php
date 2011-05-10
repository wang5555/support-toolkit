<?php
/**
 *
 * @package SupportToolkit-core
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

/**
 * @ignore
 */
if (!defined('IN_STK'))
{
	exit;
}

/**
 * phpBB wrapper class,
 * this class maintains all connections to phpBB and should be used
 * whenever a call to phpBB related code is made
 *
 * @package SupportToolkit-core
 */
class stk_core_phpbb
{
	/**
	 * @var phpbb_config A phpBB config object holding all data stored in the config.php
	 */
	public $db_config = null;

	/**
	 * @var stk_helpers_phpbb Instance of the phpBB Helper class
	 */
	private $helper = null;

	/**
	 * @var String Absolute path to the phpBB files used by the STK
	 */
	public $files_path = '';

	/**
	 * Relative path to the users phpBB installation
	 */
	const PHPBB_ROOT_PATH = './../';

	/**
	 * Construct the phpBB wrapper
	 *
	 * @param String $root_path The path to the phpBB files
	 */
	public function __construct($root_path)
	{
		$this->files_path = $root_path;

		// Setup the phpbb autoloader
		$phpbb_autoloader = new stk_autoloader($this->files_path . 'includes/', 'phpbb');
		$phpbb_autoloader->register();

		$this->helper = new stk_helpers_phpbb($this);

		// Make sure that phpBB understands the paths
		global $phpbb_root_path, $phpEx;
		$phpbb_root_path = $this->files_path;
		$phpEx = substr(PHP_EXT, 1);
	}

	/**
	 * Initialse phpBB
	 *
	 * Handle some initial basic setup for phpBB, this method handles some
	 * of the tasks performed by the phpBB's common.php
	 *
	 * @return void
	 */
	public function initialise()
	{
		define('IN_PHPBB', true);
		global $phpbb_root_path, $phpEx;	// <- Required for the includes!
		global $sql_db;						// <- Required to not break `new $sql_db`

		// Deregister globals if needed
		$this->helper->deregister_globals();

		// Check that phpBB is actaully installed
		if (false === (@include(self::PHPBB_ROOT_PATH . 'config' . PHP_EXT)) || !defined('PHPBB_INSTALLED'))
		{
			$this->helper->redirect_to_installer();
		}

		// Store the data from the config file in a config object
		$this->db_config = new phpbb_config(array(
			'dbms'				=> $dbms,
			'dbhost'			=> $dbhost,
			'dbport'			=> $dbport,
			'dbname'			=> $dbname,
			'dbuser'			=> $dbuser,
			'dbpasswd'			=> $dbpasswd,
			'table_prefix'		=> $table_prefix,
			'acm_type'			=> $acm_type,
			'load_extensions'	=> $load_extensions,
		));

		$this->helper->load_extensions();

		// Include commonly used phpBB files that can't be autoloaded
		$common_files = array('template', 'session', 'auth', 'functions', 'functions_content', 'constants', "db/{$dbms}", 'hooks/index', 'utf/utf_tools');
		foreach ($common_files as $cf)
		{
			require("{$this->files_path}includes/{$cf}" . PHP_EXT);
		}
	}

	/**
	 * Initialises the phpBB sessions, and setup phpBB
	 *
	 * @param  auth     $auth     The phpBB auth object
	 * @param  user     $user     The phpBB user object
	 * @param  template $template The phpBB template object
	 * @return void
	 */
	public function session_and_setup(auth $auth, user $user, template $template)
	{
		// First the sessions
		$user->session_begin();
		$auth->acl($user->data);

		// Manually overwrite some user vars to work with our current setup
		$user->lang_path = self::PHPBB_ROOT_PATH . 'language/';

		// Setup phpBB
		$user->setup();
	}
}
