<?php
/*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

// Security
if (!defined('_PS_VERSION_'))
	exit;

if (file_exists(dirname(__FILE__).'/lib/CloudCacheApi.php'))
  require(dirname(__FILE__).'/lib/CloudCacheApi.php');
elseif (file_exists(dirname(__FILE__).'/../modules/cloudcache/lib/CloudCacheApi.php'))
  require(dirname(__FILE__).'/../modules/cloudcache/lib/CloudCacheApi.php');

define('CLOUDCACHE_API_PORT', 80);
define('CLOUDCACHE_API_HTTP_METHOD', 'http11');
define('CLOUDCACHE_API_URI', '/xmlrpc/');
define('CLOUDCACHE_API_URL', 'api.netdna.com');
define('CLOUDCACHE_API_ZONE_URL', 'netdna-cdn.com');
define('CLOUDCACHE_API_HASH_TYPE', 'sha256');
define('CLOUDCACHE_API_PULL_ZONE_TYPE', 1);

class CloudCache extends Module
{
	/** @var _cipherTool Helper Object to encrypt API KEY */
	private $_cipherTool;

	/** @var _api Cloudcache Api Object */
	private $_api;

	/******************************************************************/
	/** Construct Method **********************************************/
	/******************************************************************/
	public function __construct()
	{
		$this->name = 'cloudcache';
		$this->tab = 'administration';
		$this->version = '1.2';
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('CloudCache');
		$this->description = $this->l('Supercharge your Shop with the CloudCache.com Content Delivery Network (CDN).');


		/* Backward compatibility */
		require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);

		if (Configuration::get('PS_CIPHER_ALGORITHM'))
			$this->_cipherTool = new Rijndael(_RIJNDAEL_KEY_, _RIJNDAEL_IV_);
		else
			$this->_cipherTool = new Blowfish(_COOKIE_KEY_, _COOKIE_IV_);

		$this->_api = new CloudcacheApi();
	}

	/**
	 * @brief Install/Uninstall Configuration variables
	 *
	 * @param install True for installation, false for uninstall
	 *
	 * @return Success or failure
	 */
	private function _setupConfigVariables($install = true)
	{
		$configVars = array(
			'CLOUDCACHE_API_USER' => '',
			'CLOUDCACHE_API_KEY' => '',
			'CLOUDCACHE_API_COMPANY_ID' => '',
			'CLOUDCACHE_API_ACTIVE' => 0,
		);

		$error = 0;
		foreach ($configVars as $varName => $value)
			if ($install)
				$error += Configuration::updateValue($varName, $value) ? 0 : 1;
			else
				$error += Configuration::deleteByName($varName) ? 0 : 1;

		return $error > 0 ? false : true;
	}

	private function _installOverride()
	{
		// Hash of the empty file in 1.5 => file name
		$files = array('810f3fa83a88b5019be31d7b80db460d' => 'classes/Tools.php');
		if (_PS_VERSION_ > '1.5')
			$files['5b917f57038acb75714cf144c9043bb4'] = 'classes/controller/FrontController.php';

		// Make sure the environment is OK
		if (!is_dir(dirname(__FILE__).'/../../override/classes/'))
			mkdir(dirname(__FILE__).'/../../override/classes/', 0777, true);
		if (_PS_VERSION_ > '1.5' && !is_dir(dirname(__FILE__).'/../../override/classes/controller/'))
			mkdir(dirname(__FILE__).'/../../override/classes/controller/', 0777);

		$errors = array();
		foreach ($files as $hash => $path)
		{
			if (file_exists(dirname(__FILE__).'/../../override/'.$path))
			{
				if (md5_file(dirname(__FILE__).'/../../override/'.$path) == $hash)
					rename(dirname(__FILE__).'/../../override/'.$path, dirname(__FILE__).'/../../override/'.$path.'.origin.php');
				elseif (md5_file(dirname(__FILE__).'/../../override/'.$path) == md5_file(dirname(__FILE__).'/override/'.$path))
					continue ;
				else
				{
					$errors[] = '/override/'.$path;
					continue ;
				}
			}
			copy(dirname(__FILE__).'/override/'.$path, dirname(__FILE__).'/../../override/'.$path);
		}

		if (count($errors))
			die('<div class="conf warn">
								<img src="../img/admin/warn2.png" alt="" title="" />'.
				$this->l('The module was successfully installed (').
				'<a href="?tab=AdminModules&configure=cloudcache&token='.Tools::getAdminTokenLite('AdminModules').'&tab_module=administration&module_name=cloudcache" style="color: blue;">'.$this->l('configure').'</a>'.
				$this->l(') but the following file already exist. Please, merge the file manually.').'<br />'.
				implode('<br />', $errors).
				'</div>');
		return true;
	}

	/******************************************************************/
	/** Install / Uninstall Methods ***********************************/
	/******************************************************************/
	public function install()
	{
		// Setup config variable with 'install' flag on
		if (!$this->_setupConfigVariables(true))
			return false;

		if (!parent::install() || !$this->registerHook('backOfficeTop'))
			return false;

		// Perform the sql install
		include(dirname(__FILE__).'/sql/sql-install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;

		return $this->_installOverride();;
	}

	/**
	 * @brief Uninstall function
	 *
	 * @return Success or failure
	 */
	public function uninstall()
	{
		// Uninstall parent and unregister Configuration
		if (!parent::uninstall())
			return false;

		// Unregister hook
		if (!$this->unregisterHook('backOfficeTop'))
			return false;

		// Remove configuration variable with 'install' flag off
		if (!$this->_setupConfigVariables(false))
			return false;

		// Uninstall SQL
		include(dirname(__FILE__).'/sql/sql-uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;

		return true;
	}

	/**
	 * @brief Check that everything is alright for Cloudcache usage.
	 *
	 * @return Array empty on success, filled with error messages on failure.
	 */
	private function _compatibilityCheck()
	{
		// Compatibility check
		$messages = array();
		if (Configuration::get('PS_CSS_THEME_CACHE') ||
			Configuration::get('PS_JS_THEME_CACHE') ||
			Configuration::get('PS_HTML_THEME_COMPRESSION') ||
			Configuration::get('PS_JS_HTML_THEME_COMPRESSION') ||
			Configuration::get('PS_HIGH_HTML_THEME_COMPRESSION'))
			$messages[] = $this->l('In order to succesfully use Cloudcache, please fix the following:');

		if (Configuration::get('PS_CSS_THEME_CACHE'))
			$messages[] = $this->l('Make sure you check "Keep CSS as original"');
		if (Configuration::get('PS_JS_THEME_CACHE'))
			$messages[] = $this->l('Make sure you check "Keep JavaScript as original"');
		if (Configuration::get('PS_HTML_THEME_COMPRESSION'))
			$messages[] = $this->l('Make sure you check "Keep HTML as original"');
		if (Configuration::get('PS_JS_HTML_THEME_COMPRESSION'))
			$messages[] = $this->l('Make sure you check "Keep inline JavaScript in HTML as original"');
		if (Configuration::get('PS_HIGH_HTML_THEME_COMPRESSION'))
			$messages[] = $this->l('Make sure you check "Keep W3C validation"');
		if (!extension_loaded('curl'))
			$messages[] = $this->l('You should ask your hosting provider to enable CURL extension in PHP (php.ini) for Cloudcache module to work.');

		// If there is any compatibility issue, just deactivate everything
		if (count($messages))
			Configuration::updateValue('CLOUDCACHE_API_ACTIVE', 0);

		return $messages;
	}

	/**
	 * @brief hookBackOfficeTop Implementation.
	 *
	 * Hook that allow to add script anywhere in the backoffice.
	 *
	 * @return Render to display
	 */
	public function hookBackOfficeTop()
	{
		$this->context->smarty->assign('isModuleActive', $this->active);
		$this->context->smarty->assign('adminToken', Tools::getAdminTokenLite('AdminModules'));

		$messages = $this->_compatibilityCheck();
		if (count($messages))
			$this->context->smarty->assign('compatibilityIssues', $messages);

		return $this->display(__FILE__, 'views/backOfficeTop.tpl');
	}

	/**
	 * @brief Empty all tables of the module.
	 */
	private function _clearTables()
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cloudcache_zone` WHERE `id_shop` = '.(int)$this->context->shop->id);
	}

	private function _getCouponUrl()
	{
		$curLang = $this->context->cookie->id_lang;

//		$prestaBaseUrl = 'http://www.prestashop.com/modules/cloudcache.png?source='.urlencode($_SERVER['HTTP_HOST']);
		$prestaBaseUrl = __PS_BASE_URI__.'modules/cloudcache/coupon.php?lang='.$curLang.'&source='.urlencode($_SERVER['HTTP_HOST']);
		if (Configuration::get('CLOUDCACHE_API_ACTIVE'))
			return $prestaBaseUrl.'&userId='.((int)Configuration::get('CLOUDCACHE_API_USER')).
				'&companyId='.urlencode(Configuration::get('CLOUDCACHE_API_COMPANY_ID'));
		return $prestaBaseUrl;
	}

	/**
	 * @brief Main Form Method
	 *
	 * @return Rendered form
	 */
	public function getContent()
	{
		if (Tools::isSubmit('SubmitCloudcacheSettings'))
		{
			// If we change the credentials, we deactivate the module
			Configuration::updateValue('CLOUDCACHE_API_ACTIVE', 0);
			// And clear the local cache for the zones
			$this->_clearTables();

			Configuration::updateValue('CLOUDCACHE_API_USER',
				Tools::getValue('cloudcache_api_user'));
			Configuration::updateValue('CLOUDCACHE_API_COMPANY_ID',
				Tools::getValue('cloudcache_api_company_id'));
			Configuration::updateValue('CLOUDCACHE_API_KEY',
				$this->_cipherTool->encrypt(Tools::getValue('cloudcache_api_key')));

			$this->context->smarty->assign('confirmMessage',
				$this->_displayConfirmation());
		}
		elseif (Tools::isSubmit('SubmitCloudcacheTestConnection'))
			$connectionTestResult = $this->_testConnection();
		elseif (Tools::isSubmit('SubmitCloudcacheAdd_zone'))
		{
			// Check http[s]
			if (substr(Tools::getValue('origin'), 0, 7) != 'http://' &&
				substr(Tools::getValue('origin'), 0, 8) != 'https://')
				$origin = 'http://'.Tools::getValue('origin');
			else
				$origin = Tools::getValue('origin');

			if (substr(Tools::getValue('vanity_domain'), 0, 7) == 'http://' ||
				substr(Tools::getValue('vanity_domain'), 0, 8) == 'https://')
				$vanity = substr(Tools::getValue('vanity_domain'), strpos(':') + 3);
			else
				$vanity = Tools::getValue('vanity_domain');

			$zone_info = array(
				'name' => Tools::getValue('name'),
				'origin' => $origin,
				'vanity_domain' => $vanity,
				'label' => Tools::getValue('label'),
				'compress' => Tools::getValue('compress'),
			);

			$action = $this->createZone(Tools::getValue('type'), $zone_info);

			if (is_array($action))
			{
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cloudcache_zone` SET
								`name` = \''.pSQL($_POST['name']).'\',
								`origin` = \''.pSQL($_POST['origin']).'\',
								`compress` = \''.(isset($_POST['compress']) ? 1: 0).'\',
								`label` = \''.pSQL($_POST['label']).'\',
								`file_type` = \''.pSQL(CLOUDCACHE_FILE_TYPE_ALL).'\',
								`cdn_url` = \''.($_POST['vanity_domain'] ? pSQL($_POST['vanity_domain']) : pSQL($_POST['name'].'.'.Configuration::get('CLOUDCACHE_API_COMPANY_ID').'.'.CLOUDCACHE_API_ZONE_URL)).'\'
								WHERE `id_zone` = '.(int)$action['id']);

				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('Zone added.')));
			}
			else
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('Error adding the zone: ').' '.pSQL($action), 'error'));
		}
		elseif (Tools::isSubmit('SubmitCloudcacheSync'))
		{
			$action = $this->_syncZonesWithServer('all');
			if (is_array($action))
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('All zones were synced.')));
			else
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('Error syncing zones: ').' '.pSQL($action), 'error'));
		}
		elseif (Tools::isSubmit('SubmitCloudcacheClearAllCache'))
		{
			$error = false;
			foreach (Db::getInstance()->ExecuteS('SELECT `id_zone`, `zone_type`, `name` FROM `'._DB_PREFIX_.'cloudcache_zone` WHERE `id_shop` = '.(int)$this->context->shop->id.' AND `id_shop` = '.(int)$this->context->shop->id) as $zone)
				if (!$this->_api->cachePurgeAll('cache', $zone['id_zone']))
				{
					$error = true;
					break;
				}

			if (!$error)
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('The cache was purged for all zones.')));
			else
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('Error purging cache for all zones.'), 'error'));
		}
		elseif (Tools::isSubmit('SubmitCloudcacheClearZoneCache'))
		{
			$zoneName = Db::getInstance()->ExecuteS('SELECT `name` FROM '._DB_PREFIX_.'cloudcache_zone
													WHERE `id_zone` = '.(int)Tools::getValue('id_zone').' AND `id_shop` = '.(int)$this->context->shop->id);

			if ($this->_api->cachePurgeAll('cache', Tools::getValue('id_zone')))
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('The cache was purged for zone:').' '.Tools::safeOutput($zoneName[0]['name'])));
			else
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('Error purging cache for zone:').' '.Tools::safeOutput($zoneName[0]['name']), 'error'));
		}
		elseif (Tools::isSubmit('SubmitCloudcacheEditZoneAction')) // display the form to edit the zone
		{
			// Get info for current zone
			$zone_info = Db::getInstance()->getRow('SELECT `id_zone`, `name`, `origin`, `compress`, `label`,
													`cdn_url`, `bw_yesterday`, `bw_last_week`, `bw_last_month`,
													`file_type`, `zone_type`
													FROM `'._DB_PREFIX_.'cloudcache_zone`
													WHERE `id_zone` = '.(int)Tools::getValue('id_zone').' AND `id_shop` = '.(int)$this->context->shop->id);

			// Clean $zone_info before sending to smarty
			$zone_info_clean = array();
			foreach ($zone_info as $key => $z)
				$zone_info_clean[$key] = pSQL($z);

			$this->context->smarty->assign('edit_zone_info', $zone_info_clean);
		}
		elseif (Tools::isSubmit('SubmitCloudcacheEdit_zone')) // save the changes on the edited zone
		{
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cloudcache_zone` SET
								`name` = \''.pSQL($_POST['name']).'\',
								`origin` = \''.pSQL($_POST['origin']).'\',
								`compress` = \''.(isset($_POST['compress']) ? 1: 0).'\',
								`label` = \''.pSQL($_POST['label']).'\',
								`cdn_url` = \''.pSQL($_POST['vanity_domain']).'\',
								`file_type` = \''.pSQL($_POST['file_type']).'\'
								WHERE `id_zone` = '.(int)Tools::getValue('id_zone'));

			$zone_info = array('id_zone' => (int)Tools::getValue('id_zone'),
									 'name' => Tools::getValue('name'),
									 'origin' => Tools::getValue('origin'),
									 'vanity_domain' => Tools::getValue('vanity_domain'),
									 'label' => Tools::getValue('label'),
									 'compress' => (bool)Tools::getValue('compress'),
									 'file_type' => Tools::getValue('file_type'));

			if (!$this->updateZone(Tools::getValue('type'), $zone_info)) // 0 : good | 1 : bad
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('The following zone was updated:').' '.pSQL(Tools::getValue('name'))));
			else
				$this->context->smarty->assign('confirmMessage',
					$this->_displayConfirmation($this->l('Error updating zone:').' '.pSQL(Tools::getValue('name')), 'error'));
		}

		$confValues = Configuration::getMultiple(array(
										'CLOUDCACHE_API_USER',
										'CLOUDCACHE_API_KEY',
										'CLOUDCACHE_API_COMPANY_ID'));

		// Set the smarty env
		$this->context->smarty->assign('serverRequestUri',
			Tools::safeOutput($_SERVER['REQUEST_URI']));
		$this->context->smarty->assign('displayName',
			Tools::safeOutput($this->displayName));

		if (isset($connectionTestResult))
			$this->context->smarty->assign('connectionTestResult',
				$connectionTestResult);

		if (isset($confValues['CLOUDCACHE_API_COMPANY_ID']))
			$this->context->smarty->assign('companyId',
				Tools::safeOutput($confValues['CLOUDCACHE_API_COMPANY_ID']));
		if (isset($confValues['CLOUDCACHE_API_USER']))
			$this->context->smarty->assign('apiUser',
				Tools::safeOutput($confValues['CLOUDCACHE_API_USER']));

		$this->context->smarty->assign('apiKey',
			Tools::safeOutput($this->_cipherTool->decrypt($confValues['CLOUDCACHE_API_KEY'])));

		$messages = $this->_compatibilityCheck();

		if (count($messages))
			$this->context->smarty->assign('compatibilityIssues', $messages);

		$this->context->smarty->assign('allAvailableZones', $this->_api->getAvailableNamespaces(true));
		$this->context->smarty->assign('prepaidBandwith', $this->getPrepaidBandwidth());

		// Get the zones
		//$zones = array('zone2' => array('name' => '','type' => 'css'));

		$zones = array();
		if (Configuration::get('CLOUDCACHE_API_USER'))
			$zones = $this->getZones('pullzone');

		// display the form
		$this->context->smarty->assign('apiActive', Configuration::get('CLOUDCACHE_API_ACTIVE'));
		$this->context->smarty->assign('zones', $zones);

		$this->context->smarty->assign('couponUrl', $this->_getCouponUrl());
		$this->context->smarty->assign('defaultOriginServerURL', (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').Configuration::get('PS_SHOP_DOMAIN'));
		if (isset($_GET['id_tab']))
		  $this->context->smarty->assign('cloudcache_id_tab', (int)$_GET['id_tab']);
		$this->context->smarty->assign('cloudcache_tracking', 'http://www.prestashop.com/modules/'.$this->name.'.png?url_site='.Tools::safeOutput($_SERVER['SERVER_NAME']).'&id_lang='.$this->context->cookie->id_lang);
		return $this->display(__FILE__, 'views/content2.tpl');
	}

	/**
	 * @brief Test the conenction to Cloudcache and the credentials.
	 *
	 * In order to test that, we just try to get the pullzones amd we check if
	 * the server reply the errorCode 0. If can't connect or other errorCode, then
	 * there is something wrong.
	 *
	 * @return True if the connection is OK, false otherwise
	 */
	private function _testConnection()
	{
		set_time_limit(0);
		
		if (count($this->_compatibilityCheck()))
			return array('<img src="../img/admin/forbbiden.gif" alt="" /><b style="color: #CC0000;">'.
				$this->l('You have compatibility issues, please fix them before using the module.').'</b>',
				'#FFD8D8');

		$zones = $this->_api->listZones('pullzone');

		if ($this->_api->getLastFaultCode())
		{
			$ret = array('<img src="../img/admin/forbbiden.gif" alt="" />
			<b style="color: #CC0000;">'.$this->l('Connection Test Failed.').'</b>',
						 '#FFD8D8', false);
			Configuration::updateValue('CLOUDCACHE_API_ACTIVE', 0);
			return $ret;
		}

		$defaultName = pSQL($this->l('prestashop'));
		// Check if default zone exists
		for ($i = 0; $i < count($zones); $i++)
			if ($zones[$i]['name'] == $defaultName)
			{
				$defaultName .= rand(1, 999);
				$i = 0;
			}

		$newZone = false;

		// If there is no zones, then create the default one
		if (!count($zones) || !Configuration::get('CLOUDCACHE_API_COMPANY_ID'))
		{
			$origin = pSQL((Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').Configuration::get('PS_SHOP_DOMAIN'));
			$r = $this->createZone('pullzone', array(
						 'name' => $defaultName,
						 'origin' => $origin,
						 'compress' => 1,
					 ));

			if (is_array($r))
			{
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cloudcache_zone` SET `name` = \''.pSQL($defaultName).'\', `origin` = \''.$origin.'\', `compress` = \'1\', `file_type` = \''.pSQL(CLOUDCACHE_FILE_TYPE_ALL).'\', `cdn_url` = \''.pSQL($r['cdn_url']).'\' WHERE `id_zone` = '.(int)$r['id']);

				$tmp = substr($r['cdn_url'], strlen($defaultName) + 1);
				$companyId = substr($tmp, 0, strlen('netdna-cdn.com') * -1 - 1);
				Configuration::updateValue('CLOUDCACHE_API_COMPANY_ID', pSQL($companyId));
			}
			else // If failure, the zonename have probably been taken
			{
				$defaultName .= rand(1, 999);
				$r = $this->createZone('pullzone', array(
							 'name' => $defaultName,
							 'origin' => $origin,
							 'compress' => 1,
						 ));
				if (is_array($r))
				{
					Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'cloudcache_zone` SET `name` = \''.pSQL($defaultName).'\', `origin` = \''.$origin.'\', `compress` = \'1\', `file_type` = \''.pSQL(CLOUDCACHE_FILE_TYPE_ALL).'\', `cdn_url` = \''.pSQL($r['cdn_url']).'\' WHERE `id_zone` = '.(int)$r['id']);
					$tmp = substr($r['cdn_url'], strlen($defaultName) + 1);
					$companyId = substr($tmp, 0, strlen('netdna-cdn.com') * -1 - 1);
					Configuration::updateValue('CLOUDCACHE_API_COMPANY_ID', pSQL($companyId));
				}
				else
					return array('<img src="../img/admin/error.png" /><b style="color: red;">'.$this->l('An error occured, impossible to create a default zone.').'</b>', '#FFD8D8', true);
			}
			$newZone = $tmp;
		}

		Configuration::updateValue('CLOUDCACHE_API_ACTIVE', 1);
		$ret = array('<img src="../img/admin/ok.gif" alt="" />
			<b style="color: green;">'.$this->l('Register').' '.
					 Configuration::get('PS_SHOP_DOMAIN').' '.
					 $this->l('on Cloudcache').'<br /></b>
				<img src="http://www.prestashop.com/modules/'.$this->name.'.png?api_user='.urlencode(Configuration::get('CLOUDCACHE_API_COMPANY_ID')).
					 '" style="display: none;" />
				', '#D6F5D6', true);
		if ($newZone)
			$ret['newZone'] = $origin;
		return $ret;
	}

	/*
	** Display a custom message for settings update
	** $text string Text to be displayed in the message
	** $type string (confirm|warn|error) Decides what color will the
	** message have (green|yellow)
	*/
	private function _displayConfirmation($text = '', $type = 'confirm')
	{
		switch ($type)
		{
			case 'confirm':
				$img = 'ok.gif';
				break ;
			case 'warn':
				$img = 'warn2.png';
				break ;
			case 'error':
				$img = 'disabled.gif';
				break ;
			default:
				die('Invalid type.');
		}

		return array(
			'class' => Tools::safeOutput($type),
			'img' => Tools::safeOutput($img),
			'text' => (empty($text) ? $this->l('Settings updated') : $text)
		);
	}

	/******************************************************************/
	/** Tools methods *************************************************/
	/******************************************************************/
	function getCurrentURL($htmlEntities = false)
	{
		$url = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		return (!empty($_SERVER['HTTPS']) ? 'https' : 'http').
			'://'.($htmlEntities ? preg_replace('/&/', '&amp;', $url): $url);
	}

	/**
	 * @brief Retrieve the bandwith from the selected zone and date range.
	 *
	 * @param zoneId    Zone Id from which we want to retrieve data
	 * @param range     Range to retrieve (daily, weekly, monthly)
	 *
	 * @return Bandwith spent by the selecte zone between the wanted date range.
	 */
	private function _getZoneTransfer($zoneId, $range)
	{
		// Associate a range with real date
		$allowedRange = array(
			'daily' => date('Y-m-d',
							 mktime(0, 0, 0, date('n'), date('j') - 1, date('Y'))),
			'weekly' => date('Y-m-d',
								mktime(0, 0, 0, date('n'), date('j') - 7, date('Y'))),
			'monthly' => date('Y-m-d',
								 mktime(0, 0, 0, date('n') - 1, date('j'), date('Y'))),
		);
		// Retrieve today's date
		$today = date('Y-m-d');

		// We check the range is correct
		if (!array_key_exists($range, $allowedRange))
			return -1;


		$companyId = Configuration::get('CLOUDCACHE_API_COMPANY_ID');

		// Retrieve the data from the server
		$r = $this->_api->getTotalTransferStats('report', $companyId, $zoneId,
				 $allowedRange[$range], $today);

		// Check if the transaction went well
		if ($this->_api->getLastFaultCode())
			return -1;

		return 0;
	}

	/**
	 * @brief Retrieves Zones from Cloudcache Server and sync with local data
	 *
	 * @param type Namespace of the zones to retrieve (pullzone, pushzone, etc)
	 *
	 * @return Array describing the zones.
	 */
	private function _syncZonesWithServer($type)
	{
		// Send the request to the API server
		$cdnZones = array();
		if ($type == 'all')
			foreach ($this->_api->getAvailableNamespaces() as $namespace)
				$cdnZones[$namespace] = $this->_api->listZones($namespace);
		else
			$cdnZones[$type] = $this->_api->listZones($type);


		$zones = array();
		// Check if the transaction went well
		if (!$this->_api->getLastFaultCode())
		{
			// Build our custom array from the retieved data
			foreach ($cdnZones as $namespace => $cdnZone)
			{
				foreach ($cdnZone as $zone)
				{
					$exists = false;

					$row = Db::getInstance()->getRow('SELECT `id_zone`, `id_shop`, `origin`, `cdn_url`, `file_type` FROM `'._DB_PREFIX_.'cloudcache_zone` WHERE `id_zone` = '.(int)$zone['id']);

					if ($row['id_zone'])
						$exists = true;

					if ($exists && $row['id_shop'] != $this->context->shop->id)
						continue ;

					$zones[(int)$zone['id']] = array(
						'id_zone' => (int)$zone['id'],
						'name' => pSQL($zone['name']),
						'origin' => ($exists ? pSQL($row['origin']) : $this->l('no data')),
						'cdn_url' => ($exists ? pSQL($row['cdn_url']) : $this->l('no data')),
						'bw_yesterday' => (int)$this->_getZoneTransfer($zone['id'], 'daily'),
						'bw_last_week' => (int)$this->_getZoneTransfer($zone['id'], 'weekly'),
						'bw_last_month' => (int)$this->_getZoneTransfer($zone['id'], 'monthly'),
						'file_type' => ($exists ? pSQL($row['file_type']) : 'none'),
						'zone_type' => pSQL($namespace),
						'id_shop' => $this->context->shop->id,
						'id_group_shop' => $this->context->shop->id_group_shop,
					);
				}
			}

			// For each zone, update or insert the new data in the database
			foreach ($zones as $id_zone => $zone_data)
				if ($zone_data['zone_type'] != 'all')
					Db::getInstance()->Execute('REPLACE INTO `'._DB_PREFIX_.'cloudcache_zone`
										(`'.implode('`,`', array_keys($zone_data)).'`)
										VALUES (\''.implode('\', \'', $zone_data).'\')');

			return $zones;
		}

		return false;
	}

	/**
	 * @brief Get Zones from the selected zone type.
	 *
	 * If the sync flag is setted to true, then retrieve data from Cloudcache servers.
	 *
	 * @note Function call thru ajax
	 *
	 * @param type Type of zone (Cloudcache namespace)
	 * @param sync Flag to know if we should ask the Cloudcache servers.
	 *
	 * @return Array describing the zones
	 */
	public function getZones($type, $sync = false)
	{
		// Check that the $type is correct an harmless for the database
		if (!in_array($type, $this->_api->getAvailableNamespaces()))
			return $this->context->smarty->assign('confirmMessage',
				$this->_displayConfirmation($this->l('Invalid zone type.'), 'error'));

		// Check on the database if $sync is false (cache)
		$zones = array();
		if (!$sync)
		{
			$d = Db::getInstance()->ExecuteS('
											SELECT `id_zone`, `id_shop`, `name`, `origin`, `compress`, `label`, `cdn_url`,
											   `bw_yesterday`, `bw_last_week`, `bw_last_month`, `file_type`, `zone_type`
											FROM `'._DB_PREFIX_.'cloudcache_zone`
											WHERE `zone_type` = \''.pSQL($type).'\' AND `id_shop` = '.(int)$this->context->shop->id);
			foreach ($d as $line)
				$zones[$line['id_zone']] = $line;
		}

		// if no result or if $sync, load data from API server
		if (($sync || !count($zones)) && Configuration::get('CLOUDCACHE_API_ACTIVE'))
			$zones = $this->_syncZonesWithServer($type);

		// Return the data array
		return $zones;
	}

	/**
	 * @brief Create a Zone on the Cloudcache Server.
	 *
	 * First create the zone thru the API then call the sync function in order to
	 * update the local data.
	 *
	 * @param type	 Type of the zone (pullzone, pushzone, etc)
	 * @param values Array describing the zone.
	 *
	 * @return True if everything went well, False otherwise
	 */
	public function createZone($type, $values)
	{
		// Check that the $type is correct an harmless for the database
		if (!in_array($type, $this->_api->getAvailableNamespaces()))
			return $this->context->smarty->assign('confirmMessage',
				$this->_displayConfirmation($this->l('Invalid zone type.'), 'error'));

		// Create the basic zone data
		$zone = array(
			'name' => pSQL($values['name']),
			'origin' => pSQL($values['origin']),
		);

		// If an optional field is set, add it to the zone
		$optionalFields = array('vanity_domain', 'vhost',
											'ip', 'compress', 'label');
		foreach ($optionalFields as $field)
			if (isset($values[$field]) && !empty($values[$field]))
				$zone[$field] = pSQL($values[$field]);

		// Then send the request to the server
		// The server return an array with the new id, vanity_ip and cdn_url
		$r = $this->_api->createZone($type, $zone);

		// Check if the transaction went well and return result
		if ($this->_api->getLastFaultCode())
			return $this->_api->getLastFaultString();

		/* // Insert the new zone in database */
		/* if ($this->_updateZoneSql($r, 'CRE)) */
		/* 	return $this->context->smarty->assign('confirmMessage', */
		/* 		$this->_displayConfirmation($this->l('Unknown internal error.'), 'error')); */

		// Sync
		$this->_syncZonesWithServer($type);

		return $r;
	}

	/**
	 * @brief Update the selected zone.
	 *
	 * @param type   Type of the zone (namespace)
	 * @param values Values of the updated zone
	 *
	 * @return True if OK, false otherwise.
	 */
	public function updateZone($type, $values)
	{
		// Check that the $type is correct an harmless for the database
		if (!in_array($type, $this->_api->getAvailableNamespaces()))
			return $this->context->smarty->assign('confirmMessage',
				$this->_displayConfirmation($this->l('Invalid zone type.'), 'error'));

		// Create the basic zone data
		$zone = array(
			'id' => (int)$values['id_zone'],
		);

		$optionalFields = array('name', 'origin', 'vhost', 'ip', 'compress', 'label');
		foreach ($optionalFields as $field)
			if (isset($values[$field]) && !empty($values[$field]))
				$zone[$field] = pSQL($values[$field]);

		// The updateZone return a bool success/error
		$r = $this->_api->updateZone($type, (int)$values['id_zone'], $zone);

		// Check if the transaction went well
		if ($this->_api->getLastFaultCode())
			return ; //die('KO '.pSQL($this->_api->getLastFaultString())); // pSQL for XSS

		return $r;
	}

	/**
	 * @brief Retrieve the paid Bandwith left
	 *
	 * @return The bandwith left.
	 */
	public function getPrepaidBandwidth()
	{
		// The namespace need to retrieve the bandwith is 'account'
		// The server reply the amount of bandwith left
		$r = $this->_api->getBandwidth('account');

		// Check if the transaction went well
		if ($this->_api->getLastFaultCode())
			return $this->l('N/A');

//		$r /= (1024 * 1024 * 1024 * 1024);
		$r /= (1000 * 1000 * 1000 * 1000);
		return round($r, 2);
	}


}
