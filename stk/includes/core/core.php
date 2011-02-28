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
	 * Instance of the phpBB wrapper class
	 * @var stk_core_phpbb
	 */
	public $phpbb = null;

	/**
	 * Construct the STK core
	 *
	 * @param stk_core_phpbb $phpbb Instance of the phpBB wrapper class
	 */
	public function __construct(stk_core_phpbb $phpbb)
	{
		$this->phpbb = $phpbb;
	}
}
