<?php
/**
 *
 * @package SupportToolkit-plugin
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

/**
 * @package SupportToolkit-plugin
 */
class stk_plugin
{
	private $plugin_class = null;
	private $plugin_class_name = '';
	private $plugin_file = '';
	private $plugin_path = '';

	public function __construct($plugin_file_path)
	{
		$this->plugin_file	= substr($plugin_file_path, strrpos($plugin_file_path, '/') + 1);
		$this->plugin_path	= substr($plugin_file_path, 0, strrpos($plugin_file_path, '/'));
		$this->plugin_class_name	= substr($this->plugin_file, 0, strrpos($this->plugin_file, '.'));
	}

	/**
	 * @return bool                True if the plugin is correct
	 * @throws ReflectionException If the class name isn't the same as the file name
	 * @throws RuntimeException    If the plugin doesn't implements the interface
	 */
	public function validate()
	{
		$this->plugin_class = new ReflectionClass($this->plugin_class_name);

		if (!$this->plugin_class->implementsInterface('stk_plugin_interface'))
		{
			throw new RuntimeException("The plugin doesn't implement the 'stk_plugin_interface'! [{$this->plugin_file}]");
		}

		return true;
	}

	public function can_run($trigger = false)
	{
		$crm = new ReflectionMethod($this->plugin_class_name, 'can_run');
		$result = $crm->invoke(null);

		if ($trigger == true && $result !== true)
		{
			trigger_error($result);
		}

		return ($result === true) ? true : false;
	}
}
