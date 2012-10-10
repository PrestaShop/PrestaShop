<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminInformationControllerCore extends AdminController
{
	public function initContent()
	{
		$this->display = 'view';
		parent::initContent();
	}

	public function renderView()
	{
		$this->tpl_view_vars = array(
			'version' => array(
				'php' => phpversion(),
				'server' => $_SERVER['SERVER_SOFTWARE'],
				'memory_limit' => ini_get('memory_limit'),
				'max_execution_time' => ini_get('max_execution_time')
			),
			'database' => array(
				'version' => Db::getInstance()->getVersion(),
				'prefix' => _DB_PREFIX_,
				'engine' => _MYSQL_ENGINE_,
			),
			'uname' => function_exists('php_uname') ? php_uname('s').' '.php_uname('v').' '.php_uname('m') : '',
			'apache_instaweb' => Tools::apacheModExists('mod_instaweb'),
			'shop' => array(
				'ps' => _PS_VERSION_,
				'url' => Tools::getHttpHost(true).__PS_BASE_URI__,
				'theme' => _THEME_NAME_,
			),
			'mail' => Configuration::get('PS_MAIL_METHOD') == 1,
			'smtp' => array(
				'server' => Configuration::get('PS_MAIL_SERVER'),
				'user' => Configuration::get('PS_MAIL_USER'),
				'password' => Configuration::get('PS_MAIL_PASSWD'),
				'encryption' => Configuration::get('PS_MAIL_SMTP_ENCRYPTION'),
				'port' => Configuration::get('PS_MAIL_SMTP_PORT'),
			),
			'user_agent' => $_SERVER['HTTP_USER_AGENT'],
		);
		$this->tpl_view_vars = array_merge($this->getTestResult(), $this->tpl_view_vars);

		return parent::renderView();
	}
	
	public function initToolBar()
	{
		return;
	}
	

	/**
	 * get all tests
	 *
	 * @return array of test results
	 */
	public function getTestResult()
	{
		// Functions list to test with 'test_system'
		// Test to execute (function/args) : lets uses the default test
		$tests = ConfigurationTest::getDefaultTests();
		$tests_op = ConfigurationTest::getDefaultTestsOp();

		$tests_errors = array(
			'phpversion' => $this->l('Update your PHP version'),
			'upload' => $this->l('Configure your server to allow file uploads'),
			'system' => $this->l('Configure your server to allow the creation of directories and files with write permissions'),
			'gd' => $this->l('Enable the GD library on your server'),
			'mysql_support' => $this->l('Enable the MySQL support on your server'),
			'config_dir' => $this->l('Set write permissions for "config" folder'),
			'cache_dir' => $this->l('Set write permissions for "cache" folder'),
			'sitemap' => $this->l('Set write permissions for "sitemap.xml" file'),
			'img_dir' => $this->l('Set write permissions for "img" folder and subfolders, recursively'),
			'log_dir' => $this->l('Set write permissions for "log" folder and subfolders, recursively'),
			'mails_dir' => $this->l('Set write permissions for "mails" folder and subfolders, recursively'),
			'module_dir' => $this->l('Set write permissions for "modules" folder and subfolders, recursively'),
			'theme_lang_dir' => $this->l('Set write permissions for "themes/')._THEME_NAME_.$this->l('/lang/" folder and subfolders, recursively'),
			'translations_dir' => $this->l('Set write permissions for "translations" folder and subfolders, recursively'),
			'customizable_products_dir' => $this->l('Set write permissions for "upload" folder and subfolders, recursively'),
			'virtual_products_dir' => $this->l('Set write permissions for "download" folder and subfolders, recursively'),
			'fopen' => $this->l('Allow the PHP fopen() function on your server'),
			'register_globals' => $this->l('Set PHP "register_global" option to "Off"'),
			'gz' => $this->l('Enable GZIP compression on your server')
		);

		$params_required_results = ConfigurationTest::check($tests);
		$params_optional_results = ConfigurationTest::check($tests_op);

		return array(
			'failRequired' => in_array('fail', $params_required_results),
			'failOptional' => in_array('fail', $params_optional_results),
			'testsErrors' => $tests_errors,
			'testsRequired' => $params_required_results,
			'testsOptional' => $params_optional_results,
		);
	}

	public function displayAjaxCheckFiles()
	{
		$this->file_list = array('missing' => array(), 'updated' => array());
		$xml = @simplexml_load_file('http://api.prestashop.com/xml/md5/'._PS_VERSION_.'.xml');
		if (!$xml)
			die(Tools::jsonEncode($this->file_list));

		$this->getListOfUpdatedFiles($xml->ps_root_dir[0]);
		die(Tools::jsonEncode($this->file_list));
	}

	public function getListOfUpdatedFiles(SimpleXMLElement $dir, $path = '')
	{
		$exclude_regexp = '(install(-dev|-new)?|themes|tools|cache|docs|download|img|localization|log|mails|translations|upload)';
		$admin_dir = basename(_PS_ADMIN_DIR_);

		foreach ($dir->md5file as $file)
		{
			$filename = preg_replace('#^admin/#', $admin_dir.'/', $path.$file['name']);
			if (preg_match('#^'.$exclude_regexp.'#', $filename))
				continue;

			if (!file_exists(_PS_ROOT_DIR_.'/'.$filename))
				$this->file_list['missing'][] = $filename;
			else
			{
				$md5_local = md5_file(_PS_ROOT_DIR_.'/'.$filename);
				if ($md5_local != (string)$file)
					$this->file_list['updated'][] = $filename;
			}
		}

		foreach ($dir->dir as $subdir)
			$this->getListOfUpdatedFiles($subdir, $path.$subdir['name'].'/');
	}
}

