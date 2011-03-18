<?php
/**
 *
 * @package Support Toolkit - Tests
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

require_once dirname(__FILE__) . '/../../stk/includes/plugin/manager.php';
require_once dirname(__FILE__) . '/../../stk/includes/plugin/plugin.php';

class stk_plugin_manager_test extends stk_test_case
{
	private $manager = null;

	protected function setUp()
	{
		$this->manager = new stk_plugin_manager(dirname(__FILE__) . '/files/');
	}

	/** 
	 * Dataprovider holding the expected build tree
	 */
	public static function expected_tree_provider()
	{
		return array(
			array('tree', array(
				'dir1' => array(
					new stk_plugin('dir1/file1.php'),
				),
				'dir2' => array(
					'subdir' => array(
						new stk_plugin('dir2/subdir/another.php'),
						new stk_plugin('dir2/subdir/subfile.php'),
					),
				),
			)),
		);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function test_plugin_dir_not_exists()
	{
		new stk_plugin_manager(dirname(__FILE__) . '/doesnt/exists/');
	}

	/**
	 * Test plugin manager initialisation
	 * @dataProvider expected_tree_provider
	 */
	public function test_plugin_initialisation($dir, $expected)
	{
		$this->manager->initialise();
		$this->assertEquals($expected, $this->manager->get_pluginlist());
	}
}
