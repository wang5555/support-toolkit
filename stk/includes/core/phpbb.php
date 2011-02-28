<?php
/**
 *
 * @package SupportToolkit
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
 * @package SupportToolkit
 */
class stk_core_phpbb
{
	/**
	 * phpBB include path
	 * @var String
	 */
	public $phpbb_root_path = '';

	/**
	 * Construct the phpBB wrapper
	 *
	 * @param String $root_path The path to the phpBB files
	 */
	public function __construct($root_path)
	{
		$this->phpbb_root_path = $root_path;

		// Setup the phpbb autoloader
		$phpbb_autoloader = new stk_autoloader($this->phpbb_root_path . '/includes/', 'phpbb');
		$phpbb_autoloader->register();
	}
}
