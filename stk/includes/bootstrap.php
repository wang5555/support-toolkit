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
$phpbb = new stk_phpbb(STK_LIB_PATH . 'phpBB/');
$phpbb->initialise();

// Set PHP error handler to ours
// @todo, add a STK error handler
set_error_handler(defined('PHPBB_MSG_HANDLER') ? PHPBB_MSG_HANDLER : 'msg_handler');

// set up caching
// Force to null cache for the time being due to: PHPBB3-9610
// @todo remove when the bug is resolved
$phpbb->db_config['acm_type'] = 'null';
$cache_factory = new phpbb_cache_factory($phpbb->db_config['acm_type']);
$cache = $cache_factory->get_service();

// Connect to DB
$db = new $sql_db();
$db->sql_connect($phpbb->db_config['dbhost'], $phpbb->db_config['dbuser'], $phpbb->db_config['dbpasswd'], $phpbb->db_config['dbname'], $phpbb->db_config['dbport'], false, defined('PHPBB_DB_NEW_LINK') ? PHPBB_DB_NEW_LINK : false);
// We do not need this any longer, unset for safety purposes
$phpbb->db_config->delete('dbpasswd');

// Grab global variables, re-cache if necessary
$config = new phpbb_config_db($db, $cache->get_driver(), CONFIG_TABLE);
set_config(null, null, null, $config);
set_config_count(null, null, null, $config);

// Construct some phpBB core classes
$auth		= new auth();
// Passes an empty array, hooks should be setup in `phpbb_hook_register`
$phpbb_hook	= new phpbb_hook(array());
$request	= new phpbb_request();
$template	= new stk_phpbb_template($phpbb);
$user		= new stk_phpbb_user($config);

// make sure request_var uses this request instance
request_var('', 0, false, false, $request); // "dependency injection" for a function

// Disable the internal cron system
$config['use_system_cron'] = true;

// Initialise the STK core
$stk = new stk_core($phpbb, $user, $config);
