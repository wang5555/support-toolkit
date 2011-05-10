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
 * Register hooks
 *
 * Function that is called by the phpBB hook system, to
 * setup any hooks that are used by the STK. This function
 * handles the adding of new hookable functions as well
 * registering any hooks that are needed.
 *
 * @param  phpbb_hook $phpbb_hook The phpBB hook object
 * @return void
 */
function phpbb_hook_register(phpbb_hook $phpbb_hook)
{
	// Allow hooks in the session handler
	$phpbb_hook->add_hook('phpbb_user_session_handler');
	$phpbb_hook->register('phpbb_user_session_handler', 'stk_user_session_handler_hook');
}

/**
 * phpBB Session hook
 *
 * Setup a hook that is called from the phpBB `phpbb_user_session_handler`
 * function, this allows the Support Toolkit to inject data while phpBB
 * is creating its session
 *
 * @param  phpbb_hook $phpbb_hook The phpBB hook object
 * @return void
 */
function stk_user_session_handler_hook(phpbb_hook $phpbb_hook)
{
	// Always include the STK common language file
	global $stk;

	$stk->add_lang('common');
}
