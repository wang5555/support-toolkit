<?php
/**
 *
 * @package Support Toolkit
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

/**
 */

/**
 * @ignore
 */
define('IN_STK', true);
if (!defined('STK_ROOT_PATH')) define('STK_ROOT_PATH', dirname(__FILE__) . '/');
if (!defined('PHP_EXT')) define('PHP_EXT', strrchr(__FILE__, '.'));
require STK_INCLUDE_PATH . 'bootstrap' . PHP_EXT;
