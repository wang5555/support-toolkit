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

$starttime = microtime(true);
$base_memory_usage = 0;
if (function_exists('memory_get_usage'))
{ 	
	$base_memory_usage = memory_get_usage();
}

// Include STK constants and main functions
require STK_ROOT_PATH . 'includes/constants' . PHP_EXT;
require STK_ROOT_PATH . 'includes/functions' . PHP_EXT;

// Report all errors, except notices and deprecation messages
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);

// Setup autoloading
require STK_INCLUDE_PATH . 'autoloader' . PHP_EXT;
$autoloader = new stk_autoloader(STK_INCLUDE_PATH, 'stk');
$autoloader->register();

// Include and setup the two main classes
$phpbb = new stk_core_phpbb(STK_LIB_PATH . 'phpBB/');
$stk = new stk_core($phpbb);
$phpbb->initialise();

// set up caching
$cache_factory = new phpbb_cache_factory($phpbb->db_config['acm_type']);
$cache = $cache_factory->get_service();

// Construct some phpBB core classes
$auth		= new auth();
$db			= new $sql_db();
// Passes an empty array, hooks should be setup in `phpbb_hook_register`
$phpbb_hook	= new phpbb_hook(array());
$request	= new phpbb_request();
$template	= new template();
$user		= new user();

// make sure request_var uses this request instance
request_var('', 0, false, false, $request); // "dependency injection" for a function

// Connect to DB
$db->sql_connect($phpbb->db_config['dbhost'], $phpbb->db_config['dbuser'], $phpbb->db_config['dbpasswd'], $phpbb->db_config['dbname'], $phpbb->db_config['dbport'], false, defined('PHPBB_DB_NEW_LINK') ? PHPBB_DB_NEW_LINK : false);

// We do not need this any longer, unset for safety purposes
// Not possible atm due to: #PHPBB3-10006
//$phpbb->db_config->delete('dbpasswd');

// Grab global variables, re-cache if necessary
$config = new phpbb_config_db($db, $cache->get_driver(), CONFIG_TABLE);
set_config(null, null, null, $config);
set_config_count(null, null, null, $config);
