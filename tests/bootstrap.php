<?php
/**
 *
 * @package testing
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

define('IN_STK', true);
define('STK_ROOT_PATH', dirname(__FILE__) . '/../stk/');
define('PHP_EXT', '.php');
$table_prefix = 'phpbb_';

error_reporting(E_ALL & ~E_DEPRECATED);

// If we are on PHP >= 6.0.0 we do not need some code
if (version_compare(PHP_VERSION, '6.0.0-dev', '>='))
{
	define('STRIP', false);
}
else
{
	@set_magic_quotes_runtime(0);
	define('STRIP', (get_magic_quotes_gpc()) ? true : false);
}

// Include common files 
require __DIR__ . '/stk_test_case' . PHP_EXT;
