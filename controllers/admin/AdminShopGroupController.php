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
*  @version  Release: $Revision: 8971 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminShopGroupControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'shop_group';
		$this->className = 'ShopGroup';
		$this->lang = false;
		$this->requiredDatabase = true;
		$this->multishop_context = Shop::CONTEXT_ALL;

		$this->addRowAction('edit');

		$this->addRowAction('delete');
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),'confirm' => $this->l('Delete selected items?')));

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->fields_list = array(
			'id_shop_group' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25,
			),
			'name' => array(
				'title' => $this->l('Group shop'),
				'width' => 'auto',
				'filter_key' => 'a!name',
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

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('ShopGroup')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Shop group name:'),
					'name' => 'name',
					'required' => true
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
					'desc' =>
						$this->l('Share orders and carts between shops of this group (you can share orders only if you share customers and available quantities)')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Share available quantities to sell:'),
					'name' => 'share_stock',
					'required' => true,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'share_stock_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'share_stock_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Share available quantities to sell between shops of this group. When changing this option, all product available quantities for this group will be reset to 0.'),
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
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		if (!($obj = $this->loadObject(true)))
			return;

		if (Shop::getTotalShops() > 1 && $obj->id)
			$disabled = array(
				'share_customer' => true,
				'share_stock' => true,
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
			'tax_rules_group' => $this->l('Tax rule groups'),
			'zone' => $this->l('Zones'),
		);

		$default_shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
		$this->tpl_form_vars = array(
			'disabled' => $disabled,
			'checked' => (Tools::getValue('addshop_group') !== false) ? true : false,
			'defaultGroup' => $default_shop->id_shop_group,
		);

		$this->fields_value = array(
			'active' => true
		);
		return parent::renderForm();
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
		$shop_group_delete_list = array();

		// test store authorized to remove
		foreach ($this->_list as $shop_group)
		{
			$shops = Shop::getShops(true, $shop_group['id_shop_group']);
			if (!empty($shops))
				$shop_group_delete_list[] = $shop_group['id_shop_group'];
		}
		$this->addRowActionSkipList('delete', $shop_group_delete_list);
	}

	public function postProcess()
	{
		if (Tools::isSubmit('delete'.$this->table) || Tools::isSubmit('status') || Tools::isSubmit('status'.$this->table))
		{
			$object = $this->loadObject();
			if (ShopGroup::getTotalShopGroup() == 1)
				$this->errors[] = Tools::displayError('You cannot delete or disable the last shop group.');
			else if ($object->haveShops())
				$this->errors[] = Tools::displayError('You cannot delete or disable a shop group which has shops using it.');

			if (count($this->errors))
				return false;
		}
		return parent::postProcess();
	}

	protected function afterAdd($new_shop_group)
	{
		//Reset available quantitites
		StockAvailable::resetProductFromStockAvailableByShopGroup($new_shop_group);
	}

	protected function afterUpdate($new_shop_group)
	{
		//Reset available quantitites
		StockAvailable::resetProductFromStockAvailableByShopGroup($new_shop_group);
	}
}


