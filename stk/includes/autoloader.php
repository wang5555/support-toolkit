<?php
/**
 *
 * @package SupportToolkit
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
 * Support Toolkit autoloader
 * The STK autoloader uses the same class rules as the phpBB Ascraeus autoloader,
 *
 *
 * Classes have to be of the form prefix_(dir_)*(classpart_)*, so directory names
 * must never contain underscores. Example: prefix_dir_subdir_class_name is a
 * valid class name, while prefix_dir_sub_dir_class_name is not.
 *
 * If every part of the class name is a directory, the last directory name is
 * also used as the filename, e.g. prefix_dir would resolve to dir/dir.php.
 *
 * @package SupportToolkit
 */
class stk_autoloader
{
	private $include_path = '';
	private $php_ext = '';
	private $prefix = '';

	/**
	 * Initialise the STK autoloader
	 *
	 * @param String $include_path The base directory where will be started when looking
	 *                             for the requested class.
	 * @param String $prefix       Class prefix used by this autoloader
	 * @param String $php_ext      Used file extension, `.php`
	 */
	public function __construct($include_path, $prefix, $php_ext = PHP_EXT)
	{
		$this->include_path = rtrim($include_path, '/') . '/';
		$this->php_ext = $php_ext;
		$this->prefix = rtrim($prefix, '_');
	}

	/**
	 * Register the autoloader
	 */
	public function register()
	{
		spl_autoload_register(array($this, 'autoload'));
	}

	/**
	 * Unregister the autoloader
	 */
	public function unregister()
	{
		spl_autoload_unregister(array($this, 'autoload'));
	}

	/**
	 * Method that handles the actual autoloading of the requested class
	 *
	 * @param  String $class The class that was requested
	 * @return void
	 */
	public function autoload($class)
	{
		if ($this->can_load($class))
		{
			$path = $this->get_path($class);
			if (!empty($path))
			{
				require $path;
			}
		}
	}

	/**
	 * Checks whether this autoloader can load the requested class
	 *
	 * @return Boolean True when the class can be loaded (based upon the class prefix,
	 *                 otherwise false
	 */
	public function can_load($class)
	{
		return (preg_match("#^{$this->prefix}_[a-zA-Z0-9_]+#", $class)) ? true : false;
	}

	/**
	 * Get the path for the requested class
	 *
	 * @param  String $class The requested class
	 * @return String The full path to the class file
	 */
	public function get_path($class)
	{
		// Split and remove the prefix
		$parts = explode('_', $class);
		array_shift($parts);

		$dirs = '';
		$file = end($parts); // Unless determined different the last part is the file name
		while (null !== ($part = array_shift($parts)))
		{
			if (!is_dir($this->include_path . $dirs . $part))
			{
				// Everything remaining is the filename
				$file = $part . ((empty($parts)) ? '' : '_' . implode('_', $parts));
				break;
			}
			$dirs .= "{$part}/";
		}

		// Check and return the path
		$full_path = $this->include_path . $dirs . $file . $this->php_ext;
		if (!file_exists($full_path))
		{
			return '';
		}
		return $full_path;
	}
}
