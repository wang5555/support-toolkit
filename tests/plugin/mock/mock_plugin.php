<?php
/**
 *
 * @package Support Toolkit - Tests
 * @copyright (c) 2011 phpBB Group
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */

require_once dirname(__FILE__) . '/../../../stk/includes/plugin/interface.php';
require_once dirname(__FILE__) . '/../../../stk/includes/plugin/base.php';

class mock_plugin extends stk_plugin_base
{
	static public function can_run()
	{
		return 'CANT_RUN';
	}
}
