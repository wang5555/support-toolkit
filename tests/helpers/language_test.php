<?php
/**
 *
 * @package Support Toolkit - Tests
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

require_once STK_ROOT_PATH . 'includes/helpers/language' . PHP_EXT;

class stk_language_test extends stk_test_case
{
	private $help = array();
	private $lang = array();
	private $lh = null;

	protected function setUp()
	{
		$this->lh = new stk_helpers_language(dirname(__FILE__) . '/files/language/', 'en', 'nl');
	}

	public function test_available_languages()
	{
		$expected = array(
			'nl',
			'en',
		);

		$this->assertEquals($expected, $this->lh->get_translations());
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_fallback_not_exists()
	{
		$helper = new stk_helpers_language(dirname(__FILE__) . '/files/language/', 'en', 'nl', 'notexists');
	}

	public function test_add_lang_user_default()
	{
		$this->assertEmpty($this->lang);
		$this->lh->set_lang($this->lang, $this->help, 'lang1');

		// Get the expected language
		$this->assertArrayHasKey('LANG1', $this->lang);
		$this->assertEquals('Zomaar een string', $this->lang['LANG1']);
	}

	public function test_add_lang_user_fallback()
	{
		$this->assertEmpty($this->lang);
		$this->lh->set_lang($this->lang, $this->help, 'lang2');
		$this->assertArrayHasKey('LANG_UNIQUE', $this->lang);
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function test_file_not_exists()
	{
		$this->lh->set_lang($this->lang, $this->help, 'notexists');
	}

	public function test_file_lang_force()
	{
		$this->assertEmpty($this->lang);
		$this->lh->set_lang($this->lang, $this->help, 'force_lang', false, false, 'en_us');
		$this->assertArrayHasKey('LANG_FORCED', $this->lang);
	}
}
