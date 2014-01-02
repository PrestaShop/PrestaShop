<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminShopGroupControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'shop_group';
		$this->className = 'ShopGroup';
		$this->lang = false;
		$this->multishop_context = Shop::CONTEXT_ALL;

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->show_toolbar = false;

		$this->fields_list = array(
			'id_shop_group' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs',
			),
			'name' => array(
				'title' => $this->l('Shop group'),
				'width' => 'auto',
				'filter_key' => 'a!name',
			),
			/*'active' => array(
				'title' => $this->l('Enabled'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'filter_key' => 'active',
				'width' => 50,
			),*/
		);

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Multistore options'),
				'fields' =>	array(
					'PS_SHOP_DEFAULT' => array(
						'title' => $this->l('Default shop:'),
						'cast' => 'intval',
						'type' => 'select',
						'identifier' => 'id_shop',
						'list' => Shop::getShops(),
						'visibility' => Shop::CONTEXT_ALL
					)
				),
				'submit' => array()
			)
		);

		parent::__construct();
	}

	public function viewAccess($disable = false)
	{
		return Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE');
	}

	public function initContent()
	{
		parent::initContent();
		
		$this->addJqueryPlugin('cooki-plugin');
		$this->addJqueryPlugin('jstree');
		$this->addCSS(_PS_JS_DIR_.'jquery/plugins/jstree/themes/classic/style.css');

		if ($this->display == 'edit')
			$this->toolbar_title[] = $this->object->name;

		$this->context->smarty->assign(array(
			'toolbar_scroll' => 1,
			'toolbar_btn' => $this->toolbar_btn,
			'title' => $this->toolbar_title,
			'selected_tree_id' => ($this->display == 'edit') ? 'tree-group-'.$this->id_object : 'tree-root',
		));
	}
	
	public function initToolbar()
	{
		parent::initToolbar();
		$this->toolbar_btn['new'] = array(
			'desc' => $this->l('Add a new shop group'),
			'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token,
		);
		$this->toolbar_btn['new_2'] = array(
			'desc' => $this->l('Add a new shop'),
			'href' => $this->context->link->getAdminLink('AdminShop').'&amp;addshop',
			'imgclass' => 'new'
		);
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Shop group'),
				'icon' => 'icon-shopping-cart'
			),
			'description' => $this->l('Warning: Enabling the "share customers" and "share orders" options is not recommended. Once activated and orders are created, you will not be able to disable these options. If you need these options, we recommend using several categories rather than several shops.'),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Shop group name'),
					'name' => 'name',
					'required' => true
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Share customers'),
					'name' => 'share_customer',
					'required' => true,
					'class' => 't',
					'is_bool' => true,
					'disabled' => ($this->id_object && $this->display == 'edit' && ShopGroup::hasDependency($this->id_object, 'customer')) ? true : false,
					'values' => array(
						array(
							'id' => 'share_customer_on',
							'value' => 1
						),
						array(
							'id' => 'share_customer_off',
							'value' => 0
						)
					),
					'desc' => $this->l('Once this option is enabled, the shops in this group will share customers. If a customer registers in any one of these shops, the account will automatically be available in the others shops of this goup. <br/><br/>Warning: you will not be able to disable this option once you have registered customers.'),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Share available quantities to sell:'),
					'name' => 'share_stock',
					'required' => true,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'share_stock_on',
							'value' => 1
						),
						array(
							'id' => 'share_stock_off',
							'value' => 0
						)
					),
					'desc' => $this->l('Share available quantities between shops of this group. When changing this option, all available products quantities will be reset to 0.'),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Share orders:'),
					'name' => 'share_order',
					'required' => true,
					'class' => 't',
					'is_bool' => true,
					'disabled' => ($this->id_object && $this->display == 'edit' && ShopGroup::hasDependency($this->id_object, 'order')) ? true : false,
					'values' => array(
						array(
							'id' => 'share_order_on',
							'value' => 1
						),
						array(
							'id' => 'share_order_off',
							'value' => 0
						)
					),
					'desc' => $this->l('Once this option is enabled (which is only possible if customers and available quantities are shared among shops), the customer\'s cart will be shared by all shops in this group. This way, any purchase started in one shop will be able to be completed in another shop from the same group. <br/><br/>Warning: You will not be able to disable this option once you\'ve started to accept orders.')
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Status:'),
					'name' => 'active',
					'required' => true,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1
						),
						array(
							'id' => 'active_off',
							'value' => 0
						)
					),
					'desc' => $this->l('Enable or disable this shop group?')
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
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
				$this->errors[] = Tools::displayError('You cannot delete or disable a shop group in use.');

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

	public function renderOptions()
	{
		if ($this->fields_options && is_array($this->fields_options))
		{
			$this->display = 'options';
			$this->show_toolbar = false;
			$helper = new HelperOptions($this);
			$this->setHelperDisplay($helper);
			$helper->id = $this->id;
			$helper->tpl_vars = $this->tpl_option_vars;
			$options = $helper->generateOptions($this->fields_options);

			return $options;
		}
	}
}


