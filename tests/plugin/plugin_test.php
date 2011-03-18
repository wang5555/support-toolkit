<?php
/**
 *
 * @package Support Toolkit - Tests
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

require_once dirname(__FILE__) . '/../../stk/includes/plugin/plugin.php';
require_once dirname(__FILE__) . '/mock/mock_plugin_no_interface.php';
require_once dirname(__FILE__) . '/mock/mock_plugin_wrong_name.php';
require_once dirname(__FILE__) . '/mock/mock_plugin.php';

class stk_plugin_test extends stk_test_case
{
	private $plugin = null;

	protected function setUp()
	{
		$this->plugin = new stk_plugin('mock/mock_plugin.php');
	}

	/**
	 * @expectedException RuntimeException
	 */
	public function test_validate_not_implementing_interface()
	{
		$p = new stk_plugin('mock/mock_plugin_no_interface.php');
		$p->validate();
	}

	/**
	 * @expectedException ReflectionException
	 */
	public function test_validate_wrong_name()
	{
		$p = new stk_plugin('mock/mock_plugin_wrong_name.php');
		$p->validate();
	}

	public function test_validate_correct()
	{
		$this->assertTrue($this->plugin->validate());
	}

	public function test_can_run_no_trigger()
	{
		$this->assertFalse($this->plugin->can_run());
	}

	/**
	 * @expectedException PHPUnit_Framework_Error_Notice
	 */
	public function test_can_run_trigger()
	{
		$this->plugin->can_run(true);
	}
}
