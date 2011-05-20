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
class stk_phpbb_user extends user
{
	/**
	 * The phpBB config object
	 * @var phpbb_config_db
	 */
	private $config = null;

	/**
	 * The STK language helper
	 * @var stk_helpers_language
	 */
	private $lang_helper = null;

	/**
	 * Construct the user class, also sets the language path
	 *
	 * @param phpbb_config_db $config The phpBB config object
	 */
	public function __construct(phpbb_config_db $config)
	{
		// Prepare language handeling
		$this->lang_path = stk_phpbb::PHPBB_ROOT_PATH . 'language/';
		$this->config = $config;
	}

	/**
	 * Override the phpBB add_lang method, so that we can
	 * redirrect the request to the STK language helper.
	 */
	public function add_lang($lang_set, $use_db = false, $use_help = false)
	{
		// Make sure the helper is set
		if (is_null($this->lang_helper))
		{
			$this->lang_helper = new stk_helpers_language($this->lang_path, basename($this->config['default_lang']), $this->data['user_lang']);
		}

		if (!is_array($lang_set))
		{
			$lang_set = array($lang_set);
		}

		foreach ($lang_set as $lang_file)
		{
			$this->lang_helper->set_lang($this->lang, $this->help, $lang_file, $use_db, $use_help, '');
		}
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
