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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


class AdminShopControllerCore extends AdminController
{
	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'shop';
		$this->className = 'Shop';
		$this->multishop_context = Shop::CONTEXT_ALL | Shop::CONTEXT_GROUP;

		$this->fields_list = array(
			'id_shop' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Shop'),
				'width' => 'auto',
				'filter_key' => 'a!name',
				'width' => 200,
			),
			'shop_group_name' => array(
				'title' => $this->l('Group Shop'),
				'width' => 150,
				'filter_key' => 'gs!name'
			),
			'category_name' => array(
				'title' => $this->l('Category Root'),
				'width' => 150,
				'filter_key' => 'cl!name'
			),
			'url' => array(
				'title' => $this->l('Shop main URL'),
				'havingFilter' => 'url',
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'filter_key' => 'active',
				'width' => 50,
			)
		);

		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),'confirm' => $this->l('Delete selected items?')));

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Shops options'),
				'fields' =>	array(
					'PS_SHOP_DEFAULT' => array(
						'title' => $this->l('Default shop:'),
						'desc' => $this->l('The default shop'),
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

	public function initToolbar()
	{
		if ($this->display == 'edit' || $this->display == 'add')
		{
			if ($shop = $this->loadObject(true))
			{
				if ((bool)$shop->id)
				{
					// adding button for delete this shop
					if ($this->tabAccess['delete'] && $this->display != 'add' && !Shop::hasDependency($shop->id))
						$this->toolbar_btn['delete'] = array(
							'short' => 'Delete',
							'href' => $this->context->link->getAdminLink('AdminShop').'&amp;id_shop='.$shop->id.'&amp;deleteshop',
							'desc' => $this->l('Delete this shop'),
							'confirm' => 1);

					// adding button for preview this shop
					if ($url_preview = $shop->getBaseURL())
						$this->toolbar_btn['preview'] = array(
							'href' => $url_preview,
							'desc' => $this->l('Home page'),
							'target' => true,
							'class' => 'previewUrl'
						);

					$this->toolbar_btn['new-url'] = array(
							'href' => $this->context->link->getAdminLink('AdminShopUrl').'&amp;id_shop='.$shop->id.'&amp;addshop_url',
							'desc' => $this->l('Add URL'),
							'class' => 'addShopUrl'
						);

				}

				if ($this->tabAccess['edit'])
				{
					$this->toolbar_btn['save'] = array(
						'short' => 'Save',
						'href' => '#todo'.$this->context->link->getAdminLink('AdminShops').'&amp;id_shop='.$shop->id,
						'desc' => $this->l('Save'),
					);

					$this->toolbar_btn['save-and-stay'] = array(
						'short' => 'SaveAndStay',
						'href' => '#todo'.$this->context->link->getAdminLink('AdminShops').'&amp;id_shop='.$shop->id,
						'desc' => $this->l('Save and stay'),
					);
				}
			}
		}

		parent::initToolbar();
		$this->context->smarty->assign('toolbar_scroll', 1);
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->_select = 'gs.name shop_group_name, cl.name category_name, CONCAT(\'http://\', su.domain, su.physical_uri, su.virtual_uri) AS url';
		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'shop_group` gs
				ON (a.id_shop_group = gs.id_shop_group)
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
				ON (a.id_category = cl.id_category AND cl.id_lang='.(int)$this->context->language->id.')
			LEFT JOIN '._DB_PREFIX_.'shop_url su
				ON a.id_shop = su.id_shop AND su.main = 1
		';
		$this->_group = 'GROUP BY a.id_shop';

		return parent::renderList();
	}

	public function displayAjaxGetCategoriesFromRootCategory()
	{
		if (Tools::isSubmit('id_category'))
		{
			$root_category = new Category((int)Tools::getValue('id_category'));
			$root_category = array(
				'id_category' => $root_category->id_category,
				'name' => $root_category->name[$this->context->language->id]
			);
			$selected_cat = array($root_category['id_category']);
			$children = Category::getChildren($root_category['id_category'], $this->context->language->id);
			foreach ($children as $child)
				$selected_cat[] = $child['id_category'];
			$helper = new Helper();
			$this->content = $helper->renderCategoryTree($root_category, $selected_cat);
		}
		parent::displayAjax();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('id_category_default'))
			$_POST['id_category'] = Tools::getValue('id_category_default');
		if ((Tools::isSubmit('status') ||
			Tools::isSubmit('status'.$this->table) ||
			(Tools::isSubmit('submitAdd'.$this->table) && Tools::getValue($this->identifier) && !Tools::getValue('active'))) &&
			$this->loadObject() && $this->loadObject()->active)
		{
			if (Tools::getValue('id_shop') == Configuration::get('PS_SHOP_DEFAULT'))
				$this->errors[] = Tools::displayError('You cannot disable the default shop.');
			else if (Shop::getTotalShops() == 1)
				$this->errors[] = Tools::displayError('You cannot disable the last shop.');
		}

		if ($this->errors)
			return false;
		return parent::postProcess();
	}

	public function processDelete()
	{
		if (!Validate::isLoadedObject($object = $this->loadObject()))
			$this->errors[] = Tools::displayError('Unable to load this shop.');
		else if (!Shop::hasDependency($object->id))
		{
			$result = Category::deleteCategoriesFromShop($object->id) && parent::processDelete();
			Tools::generateHtaccess();
			return $result;
		}
		else
			$this->errors[] = Tools::displayError('You can\'t delete this shop (customer and/or order dependency)');

		return false;
	}

	protected function afterAdd($new_shop)
	{
		if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data))
			$new_shop->copyShopData((int)Tools::getValue('importFromShop'), $import_data);
		return parent::afterAdd($new_shop);
	}

	protected function afterUpdate($new_shop)
	{
		Category::updateFromShop(Tools::getValue('categoryBox'), $new_shop->id);
		if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data))
			$new_shop->copyShopData((int)Tools::getValue('importFromShop'), $import_data);
		return parent::afterUpdate($new_shop);
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		if (Shop::getContext() == Shop::CONTEXT_GROUP)
			$this->_where .= ' AND a.id_shop_group = '.(int)Shop::getContextShopGroupID();

		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
		$shop_delete_list = array();

		// don't allow to remove shop which have dependencies (customers / orders / ... )
		foreach ($this->_list as &$shop)
		{
			if (Shop::hasDependency($shop['id_shop']))
				$shop_delete_list[] = $shop['id_shop'];
		}
		$this->addRowActionSkipList('delete', $shop_delete_list);
	}

	public function renderForm()
	{
		if (!($obj = $this->loadObject(true)))
			return;

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Shop')
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Shop name:'),
					'name' => 'name',
					'required' => true,
				)
			)
		);

		if (Shop::getTotalShops() > 1 && $obj->id)
		{
			$shop_group = new ShopGroup($obj->id_shop_group);
			$this->fields_form['input'][] = array(
				'type' => 'hidden',
				'name' => 'id_shop_group',
				'default' => $shop_group->name
			);
			$this->fields_form['input'][] = array(
				'type' => 'textShopGroup',
				'label' => $this->l('Group Shop:'),
				'name' => 'id_shop_group',
				'value' => $shop_group->name
			);
		}
		else
		{
			$options = array();
			foreach (ShopGroup::getShopGroups() as $group)
				$options[] = array(
					'id_shop_group' =>	$group->id,
					'name' =>			$group->name,
				);

			$this->fields_form['input'][] = array(
				'type' => 'select',
				'label' => $this->l('Group Shop:'),
				'name' => 'id_shop_group',
				'options' => array(
					'query' => $options,
					'id' => 'id_shop_group',
					'name' => 'name',
				),
			);
		}
		$categories = Category::getRootCategories($this->context->language->id);
		$this->fields_form['input'][] = array(
			'type' => 'select',
			'label' => $this->l('Category root:'),
			'name' => 'id_category',
			'options' => array(
				'query' => $categories,
				'id' => 'id_category',
				'name' => 'name'
			)
		);

		if (Tools::isSubmit('id_shop'))
		{
			$shop = new Shop((int)Tools::getValue('id_shop'));
			$parent = $shop->id_category;
		}
		else
			$parent = $categories[0]['id_category'];
		$this->fields_form['input'][] = array(
			'type' => 'categories_select',
			'name' => 'categoryBox',
			'label' => $this->l('Associated categories:'),
			'category_tree' => $this->initCategoriesAssociation($parent)
		);
		$this->fields_form['input'][] = array(
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
			'desc' => $this->l('Enable or disable shop')
		);

		$themes = Theme::getThemes();
		if (!isset($obj->id_theme))
			foreach ($themes as $theme)
				if (isset($theme->id))
				{
					$id_theme = $theme->id;
					break;
				}

		$this->fields_form['input'][] = array(
			'type' => 'theme',
			'label' => $this->l('Theme:'),
			'name' => 'theme',
			'values' => $themes
		);

		$this->fields_form['submit'] = array(
			'title' => $this->l('Save'),
			'class' => 'button'
		);

		if (Shop::getTotalShops() > 1 && $obj->id)
			$disabled = array('active' => false);
		else
			$disabled = false;

		$import_data = array(
			'carrier' => $this->l('Carriers'),
			'carrier_lang' => $this->l('Carriers lang'),
			'category_lang' => $this->l('Category lang'),
			'cms' => $this->l('CMS page'),
			'contact' => $this->l('Contact'),
			'country' => $this->l('Countries'),
			'currency' => $this->l('Currencies'),
			'discount' => $this->l('Discounts'),
			'employee' => $this->l('Employees'),
			'image' => $this->l('Images'),
			'lang' => $this->l('Langs'),
			'manufacturer' => $this->l('Manufacturers'),
			'module' => $this->l('Modules'),
			'hook_module' => $this->l('Module hooks'),
			'hook_module_exceptions' => $this->l('Module hook exceptions'),
			'meta_lang' => $this->l('Meta'),
			'module_country' => $this->l('Payment module country restrictions'),
			'module_group' => $this->l('Payment module customer group restrictions'),
			'module_currency' => $this->l('Payment module currency restrictions'),
			'product' => $this->l('Products'),
			'product_attribute' => $this->l('Combinations'),
			'product_lang' => $this->l('Products lang'),
			'scene' => $this->l('Scenes'),
			'stock_available' => $this->l('Available quantities for sale'),
			'store' => $this->l('Stores'),
			'warehouse' => $this->l('Warehouse'),
			'webservice_account' => $this->l('Webservice accounts'),
			'attribute' => $this->l('Attributes'),
			'attribute_group' => $this->l('Attribute groups'),
			'feature' => $this->l('Features'),
			'group' => $this->l('Customer groups'),
			'tax_rules_group' => $this->l('Tax rules groups'),
			'supplier' => $this->l('Suppliers'),
			'referrer' => $this->l('Referrers'),
		);

		// Hook for duplication of shop data
		$modules_list = Hook::getHookModuleExecList('actionShopDataDuplication');
		if (is_array($modules_list) && count($modules_list) > 0)
			foreach ($modules_list as $m)
				$import_data['Module'.ucfirst($m['module'])] = Module::getModuleName($m['module']);

		if (!$this->object->id)
			$this->fields_import_form = array(
				'radio' => array(
					'type' => 'radio',
					'label' => $this->l('Import data'),
					'name' => 'useImportData',
					'value' => 1
				),
				'select' => array(
					'type' => 'select',
					'name' => 'importFromShop',
					'label' => $this->l('Choose the shop (source)'),
					'options' => array(
						'query' => Shop::getShops(false),
						'name' => 'name'
					)
				),
				'allcheckbox' => array(
					'type' => 'checkbox',
					'label' => $this->l('Choose data to import'),
					'values' => $import_data
				),
				'desc' => $this->l('Use this option to associate data (products, modules, etc.) the same way for the selected shop')
			);

		$this->fields_value = array(
            'id_shop_group' => (Tools::getValue('id_shop_group') ? Tools::getValue('id_shop_group') :
                (isset($obj->id_shop_group)) ? $obj->id_shop_group : Shop::getContextShopGroupID()),
            'id_category' => (Tools::getValue('id_category') ? Tools::getValue('id_category') :
                (isset($obj->id_shop_group)) ? $obj->id_shop_group : Shop::getContextShopGroupID()),
			'id_theme_checked' => (isset($obj->id_theme) ? $obj->id_theme : $id_theme)
		);

		$this->tpl_form_vars = array(
			'disabled' => $disabled,
			'checked' => (Tools::getValue('addshop') !== false) ? true : false,
			'defaultShop' => (int)Configuration::get('PS_SHOP_DEFAULT'),
		);
		if (isset($this->fields_import_form))
			$this->tpl_form_vars = array_merge($this->tpl_form_vars, array('form_import' => $this->fields_import_form));

		return parent::renderForm();
	}


	/**
	 * Object creation
	 */
	public function processAdd()
	{
		if (Tools::isSubmit('id_category_default'))
			$_POST['id_category'] = (int)Tools::getValue('id_category_default');
		/* Checking fields validity */
		$this->validateRules();

		if (!count($this->errors))
		{
			$object = new $this->className();
			$this->copyFromPost($object, $this->table);
			$this->beforeAdd($object);
			if (!$object->add())
			{
				$this->errors[] = Tools::displayError('An error occurred while creating object.').
					' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
			}
			 /* voluntary do affectation here */
			else if (($_POST[$this->identifier] = $object->id) && $this->postImage($object->id) && !count($this->errors) && $this->_redirect)
			{
				$parent_id = (int)Tools::getValue('id_parent', 1);
				$this->afterAdd($object);
				$this->updateAssoShop($object->id);
				// Save and stay on same form
				if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=3&update'.$this->table.'&token='.$this->token;
				// Save and back to parent
				if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=3&token='.$this->token;
				// Default behavior (save and back)
				if (empty($this->redirect_after))
					$this->redirect_after = self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=3&token='.$this->token;
			}
		}

		$this->errors = array_unique($this->errors);
		if (count($this->errors) > 0)
        {
            $this->display = 'add';
            return;
        }

		// datas to import
		$import_data = Tools::getValue('importData');

		// specific import for stock
		if (isset($import_data['stock_available']) && isset($import_data['product']) && Tools::isSubmit('useImportData'))
		{
			$id_src_shop = (int)Tools::getValue('importFromShop');
			if ($object->getGroup()->share_stock == false)
				StockAvailable::copyStockAvailableFromShopToShop($id_src_shop, $object->id);
		}

		// if we import datas from another shop, we do not update the shop categories
		if (!isset($import_data['category']))
			Category::updateFromShop(Tools::getValue('categoryBox'), $object->id);

		return $object;
	}

	public function initCategoriesAssociation($id_root = null)
	{
		if (is_null($id_root))
			$id_root = Configuration::get('PS_ROOT_CATEGORY');
		$id_shop = (int)Tools::getValue('id_shop');
		$shop = new Shop($id_shop);
		$selected_cat = Shop::getCategories();
		if (empty($selected_cat))
		{
			// get first category root and preselect all these children
			$root_category = Category::getRootCategories();
			$children = Category::getChildren($root_category[0]['id_category'], $this->context->language->id);
			$selected_cat[] = $root_category[0]['id_category'];
			foreach ($children as $child)
				$selected_cat[] = $child['id_category'];
		}
		if (Shop::getContext() == Shop::CONTEXT_SHOP && Tools::isSubmit('id_shop'))
			$root_category = new Category($shop->id_category);
		else
			$root_category = new Category($id_root);
		$root_category = array('id_category' => $root_category->id_category, 'name' => $root_category->name[$this->context->language->id]);

		$helper = new Helper();
		return $helper->renderCategoryTree($root_category, $selected_cat, 'categoryBox', false, true);
	}
}
