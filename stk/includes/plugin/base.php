<?php
/**
 *
 * @package SupportToolkit-plugin
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

/**
 * @package SupportToolkit-plugin
 */
abstract class stk_plugin_base implements stk_plugin_interface
{
	static public function can_run()
	{
		return true;
	}
}
