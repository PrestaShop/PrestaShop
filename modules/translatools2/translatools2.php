<?php
/**
* 2007-2014 PrestaShop
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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2014 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*/

if (defined('_PS_VERSION_') === false)
	exit;

class Translatools2 extends Module
{
	public function __construct()
	{
		$this->name = 'translatools2';
		$this->tab = 'administration';
		$this->version = '1.0.0';
		$this->author = 'fmdj';
		$this->need_instance = false;
		$this->errors = array();

		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Translatools 2');
		$this->description = $this->l('Live translation for PrestaShop.');
	}

	/**
	* Install Tab
	* @return boolean
	*/
	private function installTab()
	{
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = 'AdminTranslatools2';
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = 'AdminTranslatools2';
		unset($lang);
		$tab->id_parent = -1;
		$tab->module = $this->name;
		return $tab->add();
	}

	/**
	* Uninstall Tab
	* @return boolean
	*/
	private function uninstallTab()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminTranslatools2');
		if ($id_tab)
		{
			$tab = new Tab($id_tab);
			return $tab->delete();
		}
		else
			return false;
	}

	/**
	* Insert module into datable
	* @return boolean result
	*/
	public function install()
	{
		return parent::install() && $this->installTab();
	}

	/**
	* Delete module from datable
	* @return boolean result
	*/
	public function uninstall()
	{
		return parent::uninstall() && $this->uninstallTab();
	}

	public function getContent()
	{
		Tools::redirectAdmin($this->context->link->getAdminLink('AdminTranslatools2'));
	}

	public function path()
	{
		$base = realpath(dirname(__FILE__));
		$separator = preg_match('#^/#', $base) ? '/' : '\\';
		foreach (func_get_args() as $arg)
			$base .= $separator.trim($arg, '/\\');
		return $base;
	}

	/**
	* Real work starts here
	*/

	public function isActive()
	{
		return Configuration::get('TRANSLATOOLS2_IS_ACTIVE');
	}

	public function activate()
	{
		if ($this->installVirtualLanguage())
			Configuration::updateValue('TRANSLATOOLS2_IS_ACTIVE', '1');
	}

	public function deactivate()
	{
		if ($this->uninstallVirtualLanguage())
			Configuration::updateValue('TRANSLATOOLS2_IS_ACTIVE', '0');
	}

	public function installVirtualLanguage()
	{
		if (!Language::getIdByIso('an'))
		{
			$language = new Language();
			$language->iso_code = 'an';
			$language->language_code = 'an';
			$language->name = 'LiveTranslation';
			$language->save();
			if ($language->id)
				copy(dirname(__FILE__).'/img/an.jpg', _PS_IMG_DIR_.'/l/'.$language->id.'.jpg');
			else
			{
				$this->errors[] = $this->l('Could not create virtual language.');
				return false;
			}
		}

		return true;
	}

	public function uninstallVirtualLanguage()
	{
		if ($id = Language::getIdByIso('an'))
		{
			$language = new Language($id);

			if (!$language->delete())
				return false;
		}

		return true;
	}

	public function updateVirtualLanguage()
	{

	}
}
