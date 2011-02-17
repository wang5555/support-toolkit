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

$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];
$base_memory_usage = 0;
if (function_exists('memory_get_usage'))
{ 	
	$base_memory_usage = memory_get_usage();
}

// Include constants
require STK_ROOT_PATH . 'includes/constants' . PHP_EXT;

// Report all errors, except notices and deprecation messages
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

// Setup autoloading
require STK_INCLUDE_PATH . 'autoloader' . PHP_EXT;
$autoloader = new stk_autoloader(STK_INCLUDE_PATH, 'stk');
$autoloader->register();

// Include and setup the two main classes
$phpbb = new stk_core_phpbb(STK_LIB_PATH . 'phpBB/');
$stk = new stk_core($phpbb);

// Include commonly used files that can't be autoloaded
