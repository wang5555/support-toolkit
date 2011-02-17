<?php
/**
 *
 * @package Support Toolkit - Tests
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

if (!class_exists('stk_autoloader'))
{
	require STK_ROOT_PATH . 'includes/autoloader' . PHP_EXT;
}

class stk_autoloader_test extends stk_test_case
{
	private $al = null;
	private $include_path = '';

	protected function setUp()
	{
		$this->include_path = dirname(__FILE__) . '/files/';
		$this->al = new stk_autoloader($this->include_path, 'test_', PHP_EXT);
	}

	/**
	 * Test whether classes can be loaded
	 */
	public function test_can_load()
	{
		$this->assertTrue($this->al->can_load('test_some_class'), 'Can\'t load classes with the "test_" prefix');
		$this->assertFalse($this->al->can_load('prefix_some_clas'), 'Loads classes with non-defined prefixes');
	}

	/**
	 * The class name is `test_class` and the path should be `files/class.php`
	 */
	public function test_class_first_dir()
	{
		$class_name = 'test_class';
		$expected_path = $this->include_path . 'class' . PHP_EXT;
		$this->assertEquals($expected_path, $this->al->get_path($class_name), 'Couldn\'t resolve the path for a top level class');
	}

	/**
	 * Class in a sub dir
	 * `test_dir_class` => `files/dir/class.php`
	 */
	public function test_class_dir()
	{
		$class_name = 'test_dir_class';
		$expected_path = $this->include_path . 'dir/class' . PHP_EXT;
		$this->assertEquals($expected_path, $this->al->get_path($class_name), 'Couldn\'t resolve the path for a class inside a directory');
	}

	/**
	 * Class with dir name as class
	 * `test_dir` => `files/dir/dir.php`
	 */
	public function test_class_as_dir()
	{
		$class_name = 'test_dir';
		$expected_path = $this->include_path . 'dir/dir' . PHP_EXT;
		$this->assertEquals($expected_path, $this->al->get_path($class_name), 'Couldn\'t resolve the path for a class with the directory name');
	}

	/**
	 * Deeply nested class
	 * `test_dir_sub_sub2_class_name` => `files/dir/sub/sub2/class_name.php`
	 */
	public function test_class_nested_deep()
	{
		$class_name = 'test_dir_sub_sub2_class_name';
		$expected_path = $this->include_path . 'dir/sub/sub2/class_name' . PHP_EXT;
		$this->assertEquals($expected_path, $this->al->get_path($class_name), 'Couldn\'t resolve the path for a deeply nested class');
	}
}
