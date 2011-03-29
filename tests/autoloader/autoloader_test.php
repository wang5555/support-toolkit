<?php
/**
 *
 * @package Support Toolkit - Tests
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

require_once STK_ROOT_PATH . 'includes/autoloader' . PHP_EXT;

class stk_autoloader_test extends stk_test_case
{
	private $al = null;
	static private $include_path = '';

	public function setUp()
	{
		self::$include_path = dirname(__FILE__) . '/files/';
		$this->al = new stk_autoloader(self::$include_path, 'test_', PHP_EXT);
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
	 * Test autoload result
	 */
	public function test_autoload()
	{
		$this->assertNotContains(self::$include_path . 'class' . PHP_EXT, get_included_files());
		$this->al->autoload('test_class');
		$this->assertContains(self::$include_path . 'class' . PHP_EXT, get_included_files());
	}

	/**
	 * Returns all the information for the load tests
	 */
	public static function get_load_tests()
	{
		return array(
			// `test_class` => `files/class.php
			array('test_class',						'class' . PHP_EXT,						'Couldn\'t resolve the path for a top level class'),
			// `test_dir_class` => `files/dir/class.php`
			array('test_dir_class',					'dir/class' . PHP_EXT,					'Couldn\'t resolve the path for a class inside a directory'),
			// `test_dir` => `files/dir/dir.php`
			array('test_dir',						'dir/dir' . PHP_EXT,					'Couldn\'t resolve the path for a class with the directory name'),
			// `test_dir_sub` => `files/dir/sub/sub.php`
			array('test_dir_sub',					'dir/sub/sub' . PHP_EXT,				'Couldn\'t resolve the path for a class with the name of a sub directory'),
			// Non existing
			array('test_dir_nf',					'',										'Couldn\'t resolve the path for a non existing file'),
			// `test_dir_sub_sub2_class_name` => `files/dir/sub/sub2/class_name.php`
			array('test_dir_sub_sub2_class_name',	'dir/sub/sub2/class_name' . PHP_EXT,	'Couldn\'t resolve the path for a deeply nested class'),
		);
	}

	/**
	 * Run various tests for the `get_path` method
	 * @dataProvider get_load_tests
	 */
	public function test_get_path($class, $expected, $error)
	{
		// Append the prefix for all but the empty test
		if (!empty($expected))
		{
			$expected = self::$include_path . $expected;
		}

		$this->assertEquals($expected, $this->al->get_path($class), $error);
	}
}
