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

define('_PS_INSTALL_VERSION_', '1.5.0.3');

// Generate common constants
define('PS_INSTALLATION_IN_PROGRESS', true);
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
require_once _PS_INSTALL_PATH_.'classes/simplexml.php';

@set_time_limit(300);

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
	protected $data = array();
	protected $last_time;
	protected $depth = 0;

	public function __construct()
	{
		$this->fd = fopen(_PS_INSTALL_PATH_.'log.txt', 'w');
		$this->last_time = microtime(true);
	}

	public function write($id)
	{
		$str = str_pad("[$id]", 35, ' ');
		if (isset($this->data[$id]['start']))
			$str .= str_pad(round(microtime(true) - $this->data[$id]['start'], 4).'ms', 10, ' ');
		$str .= str_pad(round(microtime(true) - $this->last_time, 4).'ms', 10, ' ');
		$this->data[$id]['str'] = str_repeat("\t", $this->depth - 1)."$str\n";
		$this->depth--;
	}

	public function start($id)
	{
		$this->data[$id] = array('start' => microtime(true));
		$this->depth++;
	}

	public function __destruct()
	{
		foreach ($this->data as $k => $info)
			if (!isset($info['str']))
				$this->write($k);

		foreach ($this->data as $info)
			fwrite($this->fd, $info['str']);
		fclose($this->fd);
	}
}
