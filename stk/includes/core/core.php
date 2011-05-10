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
 * Main STK core class
 *
 * @package SupportToolkit-core
 */
class stk_core
{
	/**
	 * The phpBB configuration data
	 * @var phpbb_config_db
	 */
	private $config = null;

	/**
	 * Instance of the phpBB wrapper class
	 * @var stk_core_phpbb
	 */
	private $phpbb = null;

	/**
	 * Instance of the STK user object
	 * @var stk_core_user
	 */
	private $user = null;

	/**
	 * Base used for all links, PHPBB_ROOT_PATH can't be used
	 * as this is an absolute path!
	 * @var string
	 */
	public $web_path = '';

	/**
	 * Construct the STK core
	 *
	 * @param stk_core_phpbb  $phpbb  Instance of the phpBB wrapper class
	 * @param stk_core_user   $user   Instance of the STK user object
	 * @param phpbb_config_db $config phpBB configuration data
	 */
	public function __construct(stk_core_phpbb $phpbb, stk_core_user $user, phpbb_config_db $config)
	{
		$this->config	= $config;
		$this->phpbb	= $phpbb;
		$this->user		= $user;

		// Find the directory name of the STK dir
		$name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');
		if (!$name)
		{
			$name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
		}

		// Replace backslashes and doubled slashes (could happen on some proxy setups)
		$name = str_replace(array('\\', '//', '/install'), '/', $name);
		$script_path = trim(dirname($name));
		$this->stk_dir = substr($script_path, strrpos($script_path, '/') + 1);	// +1 to account for the slash

		// Set the web path
		$this->web_path = generate_board_url(false) . "/{$this->stk_dir}";
	}

	/**
	 * Include STK language file
	 *
	 * A wrapper around the `$user::add_lang()` method, designed to include
	 * Support Toolkit language files.
	 * --------------------------------------------------------------------
	 * Add Language Items - use_db and use_help are assigned where needed (only use them to force inclusion)
	 *
	 * @param mixed  $lang_set   specifies the language entries to include
	 * @param string $force_lang force a specific language to be used
	 * @param bool   $use_db     internal variable for recursion, do not use
	 * @param bool   $use_help   internal variable for recursion, do not use
	 */
	public function add_lang($lang_set, $force_lang = false, $use_db = false, $use_help = false)
	{
		if (!is_array($lang_set))
		{
			$lang_set = array($lang_set);
		}

		// Internally cache some data
		static $lang_data = array();
		static $lang_dirs = array();

		// Store current phpBB data
		if (empty($lang_data))
		{
			$lang_data = array(
				'lang_path'	=> $this->user->lang_path,
				'lang_name'	=> $this->user->lang_name,
			);
		}

		// Empty the lang_name
		$this->user->lang_name = '';

		// Find out what languages we could use
		if (empty($lang_dirs))
		{
			$lang_dirs = array(
				$this->user->data['user_lang'],				// User default
				basename($this->config['default_lang']),	// Board default
				'en',										// System default
			);
	
			// Only unique dirs
			$lang_dirs = array_unique($lang_dirs);
		}

		// Switch to the STK language dir
		$this->user->lang_path = STK_ROOT_PATH . 'language/';

		// Test and include all files from the set
		foreach ($lang_set as $lang_file)
		{
			// Test all languages
			foreach ($lang_dirs as $dir)
			{
				// When forced skip all others
				if (!empty($force_lang) && $dir != $force_lang)
				{
					continue;
				}

				if (file_exists($this->user->lang_path . $dir . "/{$lang_file}" . PHP_EXT))
				{
					$this->user->lang_name = $dir;
					break;
				}
			}

			// No language file :/
			if (empty($this->user->lang_name))
			{
				trigger_error("Language file: {$lang_file}" . PHP_EXT . ' missing!', E_USER_ERROR);
			}

			// Add the file
			$this->user->add_lang($lang_file);
		}

		// Now reset the paths so phpBB can continue to operate as usual
		$this->user->lang_path = $lang_data['lang_path'];
		$this->user->lang_name = $lang_data['lang_name'];
	}
}
