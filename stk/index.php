<?php
/**
 *
 * @package SupportToolkit
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

/**
 */
define('IN_STK', true);
if (!defined('STK_ROOT_PATH')) define('STK_ROOT_PATH', dirname(__FILE__) . '/');
if (!defined('PHP_EXT')) define('PHP_EXT', strrchr(__FILE__, '.'));
require STK_ROOT_PATH . 'includes/bootstrap' . PHP_EXT;

// Load phpBB session and setup phpBB
$phpbb->session_and_setup($auth, $user, $template);

// Validate the users session
if (!$user->stk_has_valid_session())
{
	$login_box = new stk_helpers_login_box($auth, $config, $phpbb, $request, $stk, $template, $user);
	$user->stk_login($login_box);
}
