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
 * Class used for loading language files,
 * this is based upon the phpBB `user::add_lang()`
 * method. But includes language fall back.
 *
 * @package SupportToolkit-helpers
 */
class stk_helpers_language
{
	/**
	 * @var array Available translations
	 */
	private $available_translations = array();

	/**
	 * @var string Path to the used language directory
	 */
	private $lang_path = '';

	/**
	 * Setup the class
	 *
	 * @param string $lang_path Path to the language directory
	 * @param string $default   The phpBB default language
	 * @param string $user      The users language
	 * @param string $fallback  The last possible fallback, must be available
	 */
	public function __construct($lang_path, $default, $user, $fallback = 'en')
	{
		$this->lang_path = rtrim($lang_path, '/') . '/';

		// Test last fallback
		if (!is_dir($this->lang_path . $fallback))
		{
			// Hardcoded error
			trigger_error("The last language fallback <em>[{$fallback}]</em> isn't available. Please reupload this language directory!", E_USER_ERROR);
		}

		// Create the available translations array
		$this->available_translations = array_unique(array(
			$user,		// The user's language
			$default,	// Board default
			$fallback,	// The last resort
		));
	}

	/**
	 * Replacement for the phpBB `user::set_lang()` method
	 *
	 * @param  string $force_lang New STK only parameter to force a certain language to be used
	 * @return void
	 */
	public function set_lang(&$lang, &$help, $lang_file, $use_db = false, $use_help = false, $force_lang = '')
	{
		// Determine the correct language set to be used for this file
		$lang_name = '';
		if (empty($force_lang))
		{
			foreach ($this->available_translations as $trans)
			{
				if (file_exists($this->lang_path . $trans . "/{$lang_file}" . PHP_EXT))
				{
					$lang_name = $trans;
					break;
				}
			}
		}
		else
		{
			if (file_exists($this->lang_path . $force_lang . "/{$lang_file}" . PHP_EXT))
			{
				$lang_name = $force_lang;
			}
		}

		// No file!
		if (empty($lang_name))
		{
			trigger_error('Language file <em>[' . $lang_file . ']</em> couldn\'t be opened.', E_USER_ERROR);
		}

		require $this->lang_path . $lang_name . "/{$lang_file}" . PHP_EXT;
	}

	/**
	 * Get the available translations
	 *
	 * @return array The available translations
	 */
	public function get_translations()
	{
		return $this->available_translations;
	}
}
