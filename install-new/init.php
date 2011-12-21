<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('_PS_INSTALL_VERSION_', '1.5.0.0');

// Generate common constants
define('_PS_INSTALL_PATH_', dirname(__FILE__).'/');
define('_PS_INSTALL_DATA_PATH_', _PS_INSTALL_PATH_.'data/');
define('_PS_INSTALL_CONTROLLERS_PATH_', _PS_INSTALL_PATH_.'controllers/');
define('_PS_INSTALL_MODELS_PATH_', _PS_INSTALL_PATH_.'models/');
define('_PS_INSTALL_LANGS_PATH_', _PS_INSTALL_PATH_.'langs/');
define('_PS_INSTALL_FIXTURES_PATH_', _PS_INSTALL_PATH_.'fixtures/');
define('__PS_BASE_URI__', dirname(substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/'))).'/');
define('_THEME_NAME_', 'prestashop');
require_once dirname(_PS_INSTALL_PATH_).'/config/defines.inc.php';
require_once dirname(_PS_INSTALL_PATH_).'/config/defines_uri.inc.php';

// PrestaShop autoload is used to load some helpfull classes like Tools.
// Add classes used by installer bellow.
require_once _PS_ROOT_DIR_.'/config/autoload.php';
require_once _PS_ROOT_DIR_.'/config/alias.php';
require_once _PS_INSTALL_PATH_.'classes/exception.php';
require_once _PS_INSTALL_PATH_.'classes/languages.php';
require_once _PS_INSTALL_PATH_.'classes/language.php';
require_once _PS_INSTALL_PATH_.'classes/model.php';
require_once _PS_INSTALL_PATH_.'classes/session.php';
require_once _PS_INSTALL_PATH_.'classes/sqlLoader.php';
require_once _PS_INSTALL_PATH_.'classes/xmlLoader.php';

class InstallLog
{
	/**
	 * @return InstallLog
	 */
	public function getInstance()
	{
		static $instance = null;

		if (!$instance)
			$instance = new InstallLog();
		return $instance;
	}

	protected $fd;
	protected $start_time = array();
	protected $last_time;

	public function __construct()
	{
		$this->fd = fopen(_PS_INSTALL_PATH_.'log.txt', 'w');
		$this->last_time = microtime(true);
	}

	public function write($id)
	{
		$str = "[$id]";
		$str .= ' - [Time: '.round(microtime(true) - $this->last_time, 4).']';
		if (isset($this->start_time[$id]))
			$str .= ' - [Length: '.round(microtime(true) - $this->start_time[$id], 4).']';
		fwrite($this->fd, "$str\n");
	}

	public function start($id)
	{
		$this->start_time[$id] = microtime(true);
	}

	public function __destruct()
	{
		fclose($this->fd);
	}
}