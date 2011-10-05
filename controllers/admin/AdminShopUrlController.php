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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminShopUrlControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'shop_url';
		$this->className = 'ShopUrl';
	 	$this->lang = false;
	 	$this->edit = true;
		$this->delete = true;
		$this->requiredDatabase = true;
		$this->_listSkipDelete = array(1);

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

	 	$this->_select = 's.name AS shop_name, CONCAT(a.physical_uri, a.virtual_uri) AS uri';
	 	$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.id_shop = a.id_shop)';

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fieldsDisplay = array(
			'id_shop_url' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'domain' => array('title' => $this->l('Domain'), 'width' => 130, 'filter_key' => 'domain'),
			'domain_ssl' => array('title' => $this->l('Domain SSL'), 'width' => 130, 'filter_key' => 'domain'),
			'uri' => array('title' => $this->l('Uri'), 'width' => 130, 'filter_key' => 'uri'),
			'shop_name' => array('title' => $this->l('Shop name'), 'width' => 70),
			'main' => array('title' => $this->l('Main URL'), 'align' => 'center', 'activeVisu' => 'main', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'main'),
			'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'active'),
		);

		$this->template = 'adminShopUrl.tpl';

		parent::__construct();
	}

	public function postProcess()
	{
		$token = Tools::getValue('token') ? Tools::getValue('token') : $this->token;
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$object = $this->loadObject(true);
			if ($object->id && Tools::getValue('main'))
				$object->setMain();

			if ($object->main && !Tools::getValue('main'))
				$this->_errors[] = Tools::displayError('You can\'t change a main url to a non main url, you have to set an other url as main url for selected shop');

			if (($object->main || Tools::getValue('main')) && !Tools::getValue('active'))
				$this->_errors[] = Tools::displayError('You can\'t disable a main url');
			
			if ($object->canAddThisUrl(Tools::getValue('domain'), Tools::getValue('domain_ssl'), Tools::getValue('physical_uri'), Tools::getValue('virtual_uri')))
				$this->_errors[] = Tools::displayError('A shop url that use this domain and uri already exists');
			
			parent::postProcess();
			Tools::generateHtaccess(dirname(__FILE__).'/../../.htaccess', Configuration::get('PS_REWRITING_SETTINGS'), Configuration::get('PS_HTACCESS_CACHE_CONTROL'), '');
		}
		else if ((isset($_GET['status'.$this->table]) || isset($_GET['status'])) && Tools::getValue($this->identifier))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->main)
						$this->_errors[] = Tools::displayError('You can\'t disable a main url');
					else if ($object->toggleStatus())
						Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.$token);
					else
						$this->_errors[] = Tools::displayError('An error occurred while updating status.');
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else
			return parent::postProcess();
	}

	public function displayForm($isMainTab = true)
	{
		$this->content = parent::displayForm($isMainTab);

		if (!($obj = $this->loadObject(true)))
			return;
		$current_shop = Shop::initialize();

		$list_shop_with_url = array();
		foreach (Shop::getShops(false, null, true) as $id)
			$list_shop_with_url[$id] = (bool)count(ShopUrl::getShopUrls($id));

		$js_shop_url = Tools::jsonEncode($list_shop_with_url);

		$domain = Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'domain') : $current_shop->domain;
		$domain_ssl = Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'domain_ssl') : $current_shop->domain_ssl;
		$physical_uri = Validate::isLoadedObject($obj) ? $this->getFieldValue($obj, 'physical_uri') : $current_shop->physical_uri;

		$this->context->smarty->assign('tab_form', array(
			'current' => self::$currentIndex,
			'table' => $this->table,
			'token' => $this->token,
			'id' => $obj->id,
			'domain' => $domain,
			'domain_ssl' => $domain_ssl,
			'physical_uri' => $physical_uri,
			'virtual_uri' => $this->getFieldValue($obj, 'virtual_uri'),
			'getTree' => Shop::getTree(),
			'id_shop' => $obj->id_shop,
			'main' => $this->getFieldValue($obj, 'main') ? true : false,
			'active' => $this->getFieldValue($obj, 'active') ? true : false,
			'jsShopUrl' => $js_shop_url
		));
	}

	protected function afterUpdate($object)
	{
		if (Tools::getValue('main'))
			$object->setMain();
	}

	public function initContent()
	{
		if ($this->display != 'edit')
			$this->display = 'list';

		parent::initContent();
	}

}


