<?php
/**
 *
 * @package SupportToolkit-helpers
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
 * Support Toolkit save implementation of the phpBB login box function
 *
 * @package SupportToolkit-helpers
 */
class stk_helpers_login_box
{
	private $auth		= null;
	private $config		= null;
	private $phpbb		= null;
	private $request	= null;
	private $stk		= null;
	private $template	= null;
	private $user		= null;

	public function __construct(auth $auth, phpbb_config_db $config, stk_core_phpbb $phpbb, phpbb_request $request, stk_core $stk, stk_core_template $template, stk_core_user $user)
	{
		$this->auth		= $auth;
		$this->config	= $config;
		$this->phpbb	= $phpbb;
		$this->request	= $request;
		$this->stk		= $stk;
		$this->template	= $template;
		$this->user		= $user;
	}

	public function display($l_explain = '', $admin = false, $err = '')
	{
		// Print out error if user tries to authenticate as an administrator without having the privileges...
		if ($admin && !$this->auth->acl_get('a_'))
		{
			// Not authd
			// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
			if ($this->user->data['is_registered'])
			{
				add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
			}
			trigger_error('NO_AUTH_ADMIN');
		}

		// Assign credential for username/password pair
		$credential = ($admin) ? md5(unique_id()) : false;
	
		$s_hidden_fields = array(
			'sid' => $this->user->session_id,
		);

		if ($admin)
		{
			$s_hidden_fields['credential'] = $credential;
		}

		// Assign all template stuff
		$s_hidden_fields = build_hidden_fields($s_hidden_fields);
		$this->template->assign_vars(array(
			'LOGIN_EXPLAIN'			=> $this->user->lang($l_explain),

			'S_HIDDEN_FIELDS' 		=> $s_hidden_fields,
			'U_STK_LOGIN_ACTION'	=> reapply_sid($this->stk->web_path . '/index' . PHP_EXT),

			'S_ADMIN_AUTH'			=> $admin,
			'USERNAME'				=> ($admin) ? $this->user->data['username'] : '',

			'USERNAME_CREDENTIAL'	=> 'username',
			'PASSWORD_CREDENTIAL'	=> ($admin) ? 'password_' . $credential : 'password',
		));

		page_header($this->user->lang['LOGIN'], false);

		$this->template->set_filenames(array(
			'body' => 'login_body.html'
		));

		page_footer();
	}

	public function login($l_explain = '', $l_success = '', $admin = false)
	{
		// Print out error if user tries to authenticate as an administrator without having the privileges...
		if ($admin && !$this->auth->acl_get('a_'))
		{
			// Not authd
			// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
			if ($this->user->data['is_registered'])
			{
				add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
			}
			trigger_error('NO_AUTH_ADMIN');
		}

		if (!class_exists('phpbb_captcha_factory', false))
		{
			include($this->phpbb->files_path . 'includes/captcha/captcha_factory' . PHP_EXT);
		}

		// Get credential
		if ($admin)
		{
			$credential = $this->request->variable('credential', '', false, phpbb_request_interface::REQUEST);

			if (strspn($credential, 'abcdef0123456789') !== strlen($credential) || strlen($credential) != 32)
			{
				if ($this->user->data['is_registered'])
				{
					add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
				}
				trigger_error('NO_AUTH_ADMIN');
			}

			$password	= $this->request->variable('password_' . $credential, '', true);
		}
		else
		{
			$password	= $this->request->variable('password', '', true);
		}

		$username	= $this->request->variable('username', '', true);
		$autologin	= false;
		$viewonline = 0;

		// Check if the supplied username is equal to the one stored within the database if re-authenticating
		if ($admin && utf8_clean_string($username) != utf8_clean_string($this->user->data['username']))
		{
			// We log the attempt to use a different username...
			add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
			trigger_error('NO_AUTH_ADMIN_USER_DIFFER');
		}

		// If authentication is successful we redirect user to previous page
		$result = $this->auth->login($username, $password, $autologin, $viewonline, $admin);

		// If admin authentication and login, we will log if it was a success or not...
		// We also break the operation on the first non-success login - it could be argued that the user already knows
		if ($admin)
		{
			if ($result['status'] == LOGIN_SUCCESS)
			{
				add_log('admin', 'LOG_ADMIN_AUTH_SUCCESS');
			}
			else
			{
				// Only log the failed attempt if a real user tried to.
				// anonymous/inactive users are never able to go to the ACP even if they have the relevant permissions
				if ($user->data['is_registered'])
				{
					add_log('admin', 'LOG_ADMIN_AUTH_FAIL');
				}
			}
		}

		// The result parameter is always an array, holding the relevant information...
		if ($result['status'] == LOGIN_SUCCESS)
		{
			$redirect = $this->stk->web_path;
			$message = ($l_success) ? $l_success : $this->user->lang['LOGIN_REDIRECT'];
			$l_redirect = ($admin) ? $this->user->lang['PROCEED_TO_ACP'] : $this->user->lang['RETURN_INDEX'];

			// append/replace SID (may change during the session for AOL users)
			$redirect = reapply_sid($redirect);

			// Special case... the user is effectively banned, but we allow founders to login
			if (defined('IN_CHECK_BAN') && $result['user_row']['user_type'] != USER_FOUNDER)
			{
				return;
			}

			$redirect = meta_refresh(3, $redirect);
			trigger_error($message . '<br /><br />' . sprintf($l_redirect, '<a href="' . $redirect . '">', '</a>'));
		}

		// Something failed, determine what...
		if ($result['status'] == LOGIN_BREAK)
		{
			trigger_error($result['error_msg']);
		}

		// Special cases... determine
		switch ($result['status'])
		{
			case LOGIN_ERROR_ATTEMPTS:

				$captcha = phpbb_captcha_factory::get_instance('phpbb_captcha_nogd');
				$captcha->init(CONFIRM_LOGIN);
				// $captcha->reset();

				$template->assign_vars(array(
					'CAPTCHA_TEMPLATE' => $captcha->get_template(),
				));

				$err = $this->user->lang[$result['error_msg']];
			break;

			// Username, password, etc...
			default:
				$err = $this->user->lang[$result['error_msg']];

				// Assign admin contact to some error messages
				if ($result['error_msg'] == 'LOGIN_ERROR_USERNAME' || $result['error_msg'] == 'LOGIN_ERROR_PASSWORD')
				{
					$err = (!$this->config['board_contact']) ? sprintf($this->user->lang[$result['error_msg']], '', '') : sprintf($this->user->lang[$result['error_msg']], '<a href="mailto:' . htmlspecialchars($this->config['board_contact']) . '">', '</a>');
				}

			break;
		}

		$this->display($l_explain, $admin, $err);
	}
}
