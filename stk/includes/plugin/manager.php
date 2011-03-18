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
class stk_plugin_manager
{
	private $plugin_path = '';
	private $pluginlist = array();

	public function __construct($plugin_path)
	{
		if (!is_dir($plugin_path))
		{
			throw new InvalidArgumentException('PLUGIN_PATH_NOT_FOUND');
		}
		$this->plugin_path = rtrim($plugin_path, '/') . '/';
	}

	/** 
	 * Initialse the plugin manager
	 *
	 * This builds the tree and does some initial
	 * validation on the plugin files.
	 *
	 * @return void
	 */
	public function initialise()
	{
		$iterator = new RegexIterator(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->plugin_path)), '/^.+\.php$/', RegexIterator::GET_MATCH);
		foreach ($iterator as $pluginfile)
		{
			// Remove the path
			$file = preg_replace('~^' . preg_quote($this->plugin_path) . '~', '', $pluginfile[0]);

			// Build the list
			$dirparts = explode('/', $file, -1);
			$sublist = &$this->pluginlist;
			foreach ($dirparts as $dir)
			{
				if (!isset($sublist[$dir]))
				{
					$sublist[$dir] = array();
				}
				$sublist = &$sublist[$dir];
			}
			$sublist[] = new stk_plugin($file);
		}
	}

	public function get_pluginlist()
	{
		return $this->pluginlist;
	}
}
