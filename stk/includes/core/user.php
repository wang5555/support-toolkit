<?php
/**
 *
 * @package SupportToolkit-core
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
 * Wrapper class for the phpBB user/session object
 *
 * Introduces the session code for the Support Toolkit
 * as well overwrites some phpBB variables/methods to
 * assure that data is loaded correctly.
 */
class stk_core_user extends user
{
	/**
	 * Construct the user class, also sets the language path
	 */
	public function __construct()
	{
		$this->lang_path = stk_core_phpbb::PHPBB_ROOT_PATH . 'language/';
	}

	/**
	 * Handle STK login
	 */
	public function stk_login(stk_helpers_login_box $login_box)
	{
		// Need the UCP language file
		$this->add_lang('ucp');

		// Prepare the login boxes
		$admin 		= (!$this->data['is_registered']) ? false : true;
		$l_explain	= (!$admin) ? 'STK_NON_LOGIN' : 'STK_FOUNDER_ONLY';

		if (isset($_POST['login']))
		{
			// Fix a misleading string
			$this->lang['PROCEED_TO_ACP'] = $this->lang['PROCEED_TO_STK'];

			$l_success = (!$admin) ? '' : 'LOGIN_STK_SUCCESS';
			$login_box->login($l_explain, $l_success, $admin);
		}
		else
		{
			// Setup the correct box
			$login_box->display($l_explain, $admin);
		}
	}

	/**
	 * Validate user session
	 *
	 * Checks whether the current user has a session that gives him access
	 * to the Support Toolkit
	 */
	public function stk_has_valid_session()
	{
		// Must be a founder and have an active admin session
		return ($this->data['user_type'] == USER_FOUNDER && !empty($this->data['session_admin'])) ? true : false;
	}
}
