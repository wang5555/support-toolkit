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
 * The absolute path to the includes directory
 */
define('STK_INCLUDE_PATH', STK_ROOT_PATH . 'includes/');

/**
 * The absolute path to the lib directory
 */
define('STK_LIB_PATH', STK_ROOT_PATH . 'lib/');

if (!defined('E_DEPRECATED'))
{
	/**
	 * @ignore
	 */
	define('E_DEPRECATED', 8192);
}
