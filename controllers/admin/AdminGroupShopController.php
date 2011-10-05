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

class AdminGroupShopControllerCore extends AdminController
{
	public function __construct()
	{
	 	$this->table = 'group_shop';
		$this->className = 'GroupShop';
	 	$this->lang = false;
	 	$this->edit = true;
		$this->delete = false;
		$this->requiredDatabase = true;

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fieldsDisplay = array(
			'id_group_shop' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('County'), 'width' => 130, 'filter_key' => 'b!name'),
			'active' => array('title' => $this->l('Enabled'), 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false, 'filter_key' => 'active'),
		);

		$this->template = 'adminGroupShop.tpl';

		parent::__construct();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('delete'.$this->table) || Tools::isSubmit('status') || Tools::isSubmit('status'.$this->table))
		{
			$object = $this->loadObject();
			if (GroupShop::getTotalGroupShops() == 1)
				$this->_errors[] = Tools::displayError('You cannot delete or disable the last groupshop.');
			else if ($object->haveShops())
				$this->_errors[] = Tools::displayError('You cannot delete or disable a groupshop which have this shops using it.');

			if (count($this->_errors))
				return false;
		}
		return parent::postProcess();
	}

	public function afterAdd($new_group_shop)
	{
		if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data))
			$new_group_shop->copyGroupShopData(Tools::getValue('importFromShop'), $import_data);
	}

	public function afterUpdate($new_group_shop)
	{
		if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data))
			$new_group_shop->copyGroupShopData(Tools::getValue('importFromShop'), $import_data);
	}

	public function displayForm($is_main_tab = true)
	{
		parent::displayForm($is_main_tab);

		if (!($obj = $this->loadObject(true)))
			return;

		if (Shop::getTotalShops() > 1 && $obj->id)
			$disabled = 'disabled="disabled"';
		else
			$disabled = '';

		$import_data = array(
			'attribute_group' => $this->l('Attribute groups'),
			'attribute' => $this->l('Attributes'),
			//'customer_group' => $this->l('Customer groups'),
			'feature' => $this->l('Features'),
			'group' => $this->l('Groups'),
			'manufacturer' => $this->l('Manufacturers'),
			'supplier' => $this->l('Suppliers'),
			'tax_rules_group' => $this->l('Tax rules groups'),
			'zone' => $this->l('Zones'),
		);

		$this->context->smarty->assign('tab_form', array(
			'current' => self::$currentIndex,
			'table' => $this->table,
			'token' => $this->token,
			'id' => $obj->id,
			'name' => $this->getFieldValue($obj, 'name'),
			'disabled' => $disabled,
			'share_customer' => $this->getFieldValue($obj, 'share_customer') ? true : false,
			'share_stock' => $this->getFieldValue($obj, 'share_stock') ? true : false,
			'share_order' => $this->getFieldValue($obj, 'share_order') ? true : false,
			'active' => $this->getFieldValue($obj, 'active') ? true : false,
			'importData' => $import_data,
			'getTree' => Shop::getTree(),
			'checked' => (Tools::getValue('addgroup_shop') !== false) ? true : false,
			'defaultGroup' => Shop::getInstance(Configuration::get('PS_SHOP_DEFAULT'))->getGroupID()
		));
	}

	public function initContent()
	{
		if ($this->display != 'edit')
			$this->display = 'list';

		parent::initContent();
	}

}


