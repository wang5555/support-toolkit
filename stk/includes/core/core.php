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
	 * The STK language helper
	 * @var stk_helpers_language
	 */
	private $lang_helper = null;

	/**
	 * Instance of the phpBB wrapper class
	 * @var stk_phpbb
	 */
	private $phpbb = null;

	/**
	 * Instance of the STK user object
	 * @var stk_phpbb_user
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
	 * @param stk_phpbb  $phpbb  Instance of the phpBB wrapper class
	 * @param stk_phpbb_user   $user   Instance of the STK user object
	 * @param phpbb_config_db $config phpBB configuration data
	 */
	public function __construct(stk_phpbb $phpbb, stk_phpbb_user $user, phpbb_config_db $config)
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
	public function add_lang($lang_set, $force_lang = '', $use_db = false, $use_help = false)
	{
		// Make sure the helper is loaded
		if (is_null($this->lang_helper))
		{
			$this->lang_helper = new stk_helpers_language(STK_ROOT_PATH . 'language', basename($this->config['default_lang']), $this->user->data['user_lang']);
		}

		if (!is_array($lang_set))
		{
			$lang_set = array($lang_set);
		}

		foreach ($lang_set as $lang_file)
		{
			$this->lang_helper->set_lang($this->user->lang, $this->user->help, $lang_file, $use_db, $use_help, $force_lang);
		}
	}
}
