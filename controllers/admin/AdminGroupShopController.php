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
		$this->requiredDatabase = true;

		$this->addRowAction('edit');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fieldsDisplay = array(
			'id_group_shop' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25,
			),
			'name' => array(
				'title' => $this->l('Group shop'),
				'width' => 'auto',
				'filter_key' => 'b!name',
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'filter_key' => 'active',
				'width' => 50,
			),
		);

		parent::__construct();
	}

	public function initForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('GroupShop')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('GroupShop name:'),
					'name' => 'name'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Share customers:'),
					'name' => 'share_customer',
					'required' => true,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'share_customer_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'share_customer_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Share customers between shops of this group')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Share orders:'),
					'name' => 'share_order',
					'required' => true,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'share_order_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'share_order_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Share orders and carts between shops of this group (you can share orders only if you share customers and stock)')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status:'),
					'name' => 'active',
					'required' => true,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Enable or disable group shop')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		if (!($obj = $this->loadObject(true)))
			return;

		if (Shop::getTotalShops() > 1 && $obj->id)
			$disabled = array(
				'share_customer' => true,
				'share_order' => true,
				'active' => false
			);
		else
			$disabled = false;

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

		if (!$this->object->id)
			$this->fields_import_form = array(
				'legend' => array(
					'title' => $this->l('Import data from another group shop')
				),
				'label' => $this->l('Duplicate data from group shop'),
				'checkbox' => array(
					'type' => 'checkbox',
					'label' => $this->l('Duplicate data from group shop'),
					'name' => 'useImportData',
					'value' => 1
				),
				'list' => array(
					'type' => 'select',
					'name' => 'importFromShop',
					'options' => array(
						'query' => Shop::getTree(),
						'name' => 'name'
					)
				),
				'allcheckbox' => array(
					'type' => 'checkbox',
					'values' => $import_data
				),
				'desc' => $this->l('Use this option to associate data (products, modules, etc.) the same way as the selected shop')
			);

		$this->tpl_form_vars = array(
			'disabled' => $disabled,
			'checked' => (Tools::getValue('addgroup_shop') !== false) ? true : false,
			'defaultGroup' => Shop::getInstance(Configuration::get('PS_SHOP_DEFAULT'))->getGroupID(),
		);
		if (isset($this->fields_import_form))
			$this->tpl_form_vars = array_merge($this->tpl_form_vars, array('form_import' => $this->fields_import_form));

		return parent::initForm();
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
}


