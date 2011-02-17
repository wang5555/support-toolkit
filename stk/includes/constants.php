<?php
/**
 *
 * @package Support Toolkit
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

// Some paths for the lazyones
define('STK_INCLUDE_PATH', STK_ROOT_PATH . 'includes/');
define('STK_LIB_PATH', STK_ROOT_PATH . 'lib/');

// Make sure that error reporting can be used correctly
if (!defined('E_DEPRECATED'))
{
	define('E_DEPRECATED', 8192);
}
