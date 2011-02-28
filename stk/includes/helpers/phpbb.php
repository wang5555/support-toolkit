<?php
/**
 *
 * @package SupportToolkit-helpers
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
 * Some helper functions for the phpBB core
 *
 * @package SupportToolkit-helpers
 */
class stk_helpers_phpbb
{
	/**
	 * @var stk_core_phpbb Instance of the phpBB wrapper object
	 */
	private $phpbb = null;

	/**
	 * Setup the helper class
	 *
	 * @param stk_core_phpbb $phpbb Instance of the main phpBB core object
	 */
	public function __construct(stk_core_phpbb $phpbb)
	{
		$this->phpbb = $phpbb;
	}

	/**
	 * Do some checking, whether the globals have to be deregistered and check
	 * for magic quotes
	 *
	 * @return void
	 */
	public function deregister_globals()
	{
		// If we are on PHP >= 6.0.0 we do not need some code
		if (version_compare(PHP_VERSION, '6.0.0-dev', '>='))
		{
			/**
			* @ignore
			*/
			define('STRIP', false);
		}
		else
		{
			@set_magic_quotes_runtime(0);
		
			// Be paranoid with passed vars
			if (@ini_get('register_globals') == '1' || strtolower(@ini_get('register_globals')) == 'on' || !function_exists('ini_get'))
			{
				$this->_deregister_globals();
			}
		
			define('STRIP', (get_magic_quotes_gpc()) ? true : false);
		}
	}

	/**
	 * Remove variables created by register_globals from the global scope
	 *
	 * @author Matt Kavanagh
	 * @return void
	 */
	private function _deregister_globals()
	{
		$not_unset = array(
			'GLOBALS'			=> true,
			'_GET'				=> true,
			'_POST'				=> true,
			'_COOKIE'			=> true,
			'_REQUEST'			=> true,
			'_SERVER'			=> true,
			'_SESSION'			=> true,
			'_ENV'				=> true,
			'_FILES'			=> true,
			'phpEx'				=> true,
			'phpbb_root_path'	=> true
		);
	
		// Not only will array_merge and array_keys give a warning if
		// a parameter is not an array, array_merge will actually fail.
		// So we check if _SESSION has been initialised.
		if (!isset($_SESSION) || !is_array($_SESSION))
		{
			$_SESSION = array();
		}
	
		// Merge all into one extremely huge array; unset this later
		$input = array_merge(
			array_keys($_GET),
			array_keys($_POST),
			array_keys($_COOKIE),
			array_keys($_SERVER),
			array_keys($_SESSION),
			array_keys($_ENV),
			array_keys($_FILES)
		);
	
		foreach ($input as $varname)
		{
			if (isset($not_unset[$varname]))
			{
				// Hacking attempt. No point in continuing unless it's a COOKIE
				if ($varname !== 'GLOBALS' || isset($_GET['GLOBALS']) || isset($_POST['GLOBALS']) || isset($_SERVER['GLOBALS']) || isset($_SESSION['GLOBALS']) || isset($_ENV['GLOBALS']) || isset($_FILES['GLOBALS']))
				{
					exit;
				}
				else
				{
					$cookie = &$_COOKIE;
					while (isset($cookie['GLOBALS']))
					{
						foreach ($cookie['GLOBALS'] as $registered_var => $value)
						{
							if (!isset($not_unset[$registered_var]))
							{
								unset($GLOBALS[$registered_var]);
							}
						}
						$cookie = &$cookie['GLOBALS'];
					}
				}
			}
	
			unset($GLOBALS[$varname]);
		}
	
		unset($input);
	}

	/**
	 * Load extensions
	 *
	 * phpBB can load various extensions if needed, this method attempts the same
	 * thing if needed
	 */
	public function load_extensions()
	{
		if (!empty($this->phpbb->db_config['load_extensions']) && function_exists('dl'))
		{
			$load_extensions = explode(',', $this->phpbb->db_config['load_extensions']);
			foreach ($load_extensions as $extension)
			{
				@dl(trim($extension));
			}
		}
	}

	/**
	 * Redirect the user to the phpBB installer
	 *
	 * @return void
	 */
	public function redirect_to_installer()
	{
		// Redirect the user to the installer
		// We have to generate a full HTTP/1.1 header here since we can't guarantee to have any of the information
		// available as used by the redirect function
		$server_name = (!empty($_SERVER['HTTP_HOST'])) ? strtolower($_SERVER['HTTP_HOST']) : ((!empty($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME'));
		$server_port = (!empty($_SERVER['SERVER_PORT'])) ? (int) $_SERVER['SERVER_PORT'] : (int) getenv('SERVER_PORT');
		$secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 1 : 0;

		$script_name = (!empty($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : getenv('PHP_SELF');
		if (!$script_name)
		{
			$script_name = (!empty($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : getenv('REQUEST_URI');
		}

		// Remove the "stk/" directory from the path
		$script_path = dirname($script_name);
		$script_path = trim(substr($script_path, 0, strrpos($script_path, '/')));

		// Replace any number of consecutive backslashes and/or slashes with a single slash
		// (could happen on some proxy setups and/or Windows servers)
		$script_path = $script_path . '/install/index' . PHP_EXT;
		$script_path = preg_replace('#[\\\\/]{2,}#', '/', $script_path);

		$url = (($secure) ? 'https://' : 'http://') . $server_name;

		if ($server_port && (($secure && $server_port <> 443) || (!$secure && $server_port <> 80)))
		{
			// HTTP HOST can carry a port number...
			if (strpos($server_name, ':') === false)
			{
				$url .= ':' . $server_port;
			}
		}

		$url .= $script_path;
		header('Location: ' . $url);
		exit;
	}
}
