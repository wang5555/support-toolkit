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
	 * @var auth Instance of the phpBB Auth object
	 */
	public $auth = null;

	/**
	 * @var phpbb_cache_service Instance of the phpBB Cache object
	 */
	public $cache = null;

	/**
	 * @var phpbb_config Instance of the phpBB config object
	 */
	public $config = null;

	/**
	 * @var dbal Instance of the phpBB DBAL
	 */
	public $db = null;

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
	 * @var phpbb_request Instance of the phpBB request class
	 */
	public $request = null;

	/**
	 * @var template Instance of the phpBB template object
	 */
	public $template = null;

	/**
	 * @var session Instance of the phpBB user/session object
	 */
	public $user = null;

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

		// A little bit of trickery to store the common phpBB objects
		// in this class but at the same time, not braking phpBB itself
		global $auth, $cache, $config, $db, $request, $template, $user;
		$this->auth		=& $auth;
		$this->cache	=& $cache;
		$this->config	=& $config;
		$this->db		=& $db;
		$this->request	=& $request;
		$this->template	=& $template;
		$this->user		=& $user;

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
		$common_files = array('template', 'session', 'auth', 'functions', 'functions_content', 'constants', "db/{$dbms}", 'utf/utf_tools');
		foreach ($common_files as $cf)
		{
			require("{$this->files_path}includes/{$cf}" . PHP_EXT);
		}
	}
}
