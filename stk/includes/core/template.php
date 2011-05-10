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
 * Wrapper class for the phpBB template object.
 *
 * This class overloads some of the phpBB methods to
 * assure proper working within the Support Toolkit
 */
class stk_core_template extends template
{
	/**
	 * Construct the template object
	 */
	public function __construct(stk_core_phpbb $phpbb)
	{
		// Include the template functions
		if (!class_exists('template_compile'))
		{
			include($phpbb->files_path . 'includes/functions_template' . PHP_EXT);
		}
	}

	/**
	 * Set template location
	 *
	 * Overload the phpBB template::set_template method, sets the
	 * proper path to the STK template directory and removes some
	 * logic that the STK doesn't need
	 */
	public function set_template()
	{
		// Hardcoded file location
		$stk_template_loc = STK_ROOT_PATH . 'style/template';

		if (file_exists($stk_template_loc))
		{
			$this->root = $stk_template_loc;
			$this->cachepath = stk_core_phpbb::PHPBB_ROOT_PATH . 'cache/tpl_STK_';

			$user->theme['template_storedb']		= false;
			$user->theme['template_inherits_id']	= false;
		}
		else
		{
			trigger_error('Support Toolkit template path could not be found', E_USER_ERROR);
		}

		$this->_rootref = &$this->_tpldata['.'][0];

		return true;
	}
}
