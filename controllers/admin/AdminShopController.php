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

		$this->fieldsDisplay = array(
			'id_shop' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Shop'),
				'width' => 'auto',
				'filter_key' => 'b!name'
			),
			'group_shop_name' => array(
				'title' => $this->l('Group Shop'),
				'width' => 150
			),
			'category_name' => array(
				'title' => $this->l('Category Root'),
				'width' => 150
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

		$this->options = array(
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
					if ($this->tabAccess['delete']  && $this->display != 'add'  && !Shop::has_dependency($shop->id))
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
							'desc' => $this->l('Add url'),
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
		$this->context->smarty->assign('toolbar_fix', 1);
	}

	public function initContent()
	{
		$shops =  Shop::getShopWithoutUrls();
		if (count($shops))
		{
		 	$shop_url_configuration = '';
			foreach ($shops as $shop)
				$shop_url_configuration .= sprintf($this->l('No url is configured for shop: %s'), '<b>'.$shop['name'].'</b>').' <a href="'.$this->context->link->getAdminLink('AdminShopUrl').'&addshop_url&id_shop='.$shop['id_shop'].'">'.$this->l('click here').'</a><br />';
			$this->content .= '<div class="warn">'.$shop_url_configuration.'</div>';
		}
		parent::initContent();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->_select = 'gs.name group_shop_name, cl.name category_name';
	 	$this->_join = '
	 		LEFT JOIN `'._DB_PREFIX_.'group_shop` gs
	 			ON (a.id_group_shop = gs.id_group_shop)
	 		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
	 			ON (a.id_category = cl.id_category AND cl.id_lang='.(int)$this->context->language->id.')';
	 	$this->_group = 'GROUP BY a.id_shop';

	 	return parent::renderList();
	}

	public function postProcess()
	{
		if ((Tools::isSubmit('status') ||
			Tools::isSubmit('status'.$this->table) ||
			(Tools::isSubmit('submitAdd'.$this->table) && Tools::getValue($this->identifier) && !Tools::getValue('active'))) &&
			$this->loadObject() && $this->loadObject()->active)
		{
			if (Tools::getValue('id_shop') == Configuration::get('PS_SHOP_DEFAULT'))
				$this->_errors[] = Tools::displayError('You cannot disable the default shop.');
			else if (Shop::getTotalShops() == 1)
				$this->_errors[] = Tools::displayError('You cannot disable the last shop.');
		}

		if ($this->_errors)
			return false;
		return parent::postProcess();
	}

	public function processDelete($token)
	{
		if (!Validate::isLoadedObject($object = $this->loadObject()))
			$this->_errors[] = Tools::displayError('Unable to load this shop.');
		else if(!Shop::has_dependency($object->id))
			return parent::processDelete($token);
		else
			$this->_errors[] = Tools::displayError('You can\'t delete this shop (customer and/or order dependency)');

		return false;
	}
	public function afterAdd($new_shop)
	{
		if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data))
			$new_shop->copyShopData((int)Tools::getValue('importFromShop'), $import_data);
	}

	public function afterUpdate($new_shop)
	{
		if (Tools::getValue('useImportData') && ($import_data = Tools::getValue('importData')) && is_array($import_data))
			$new_shop->copyShopData((int)Tools::getValue('importFromShop'), $import_data);
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
		$shop_delete_list = array();

		// test store authorized to remove
		foreach ($this->_list as $shop)
		{
			if (Shop::has_dependency($shop['id_shop']))
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
			$group_shop = new GroupShop($obj->id_group_shop);
			$this->fields_form['input'][] = array(
				'type' => 'hidden',
				'name' => 'id_group_shop',
				'default' => $group_shop->name
			);
			$this->fields_form['input'][] = array(
				'type' => 'textGroupShop',
				'label' => $this->l('Group Shop:'),
				'name' => 'id_group_shop',
				'value' => $group_shop->name
			);
		}
		else
		{
			$options = array();
			foreach (GroupShop::getGroupShops() as $group)
				$options[] = array(
					'id_group_shop' =>	$group->id,
					'name' =>			$group->name,
				);

			$this->fields_form['input'][] = array(
				'type' => 'select',
				'label' => $this->l('Group Shop:'),
				'name' => 'id_group_shop',
				'options' => array(
					'query' => $options,
					'id' => 'id_group_shop',
					'name' => 'name'
				)
			);
		}
		$categories = Category::getCategories($this->context->language->id, false, false);
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
		foreach ($themes as $i => $theme)
			$themes[$i]['checked'] = ((!$obj->id && $i == 0) || $obj->id_theme == $theme['id_theme']) ? true : false;

		$this->fields_form['input'][] = array(
			'type' => 'theme',
			'label' => $this->l('Theme:'),
			'name' => 'theme',
			'values' => $themes
		);

		$this->fields_form['submit'] = array(
			'title' => $this->l('   Save   '),
			'class' => 'button'
		);

		if (Shop::getTotalShops() > 1 && $obj->id)
			$disabled = array(
				'active' => false
			);
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
			'image' => $this->l('Images'),
			'lang' => $this->l('Langs'),
			'manufacturer' => $this->l('Manufacturers'),
			'module' => $this->l('Modules'),
			'hook_module' => $this->l('Modules hook'),
			'hook_module_exceptions' => $this->l('Modules hook exceptions'),
			'meta_lang' => $this->l('Meta'),
			'module_country' => $this->l('Payment module country restrictions'),
			'module_group' => $this->l('Payment module customer group restrictions'),
			'module_currency' => $this->l('Payment module currency restrictions'),
			'product' => $this->l('Products'),
			'product_lang' => $this->l('Products lang'),
			'scene' => $this->l('Scenes'),
			'stock_available' => $this->l('Available quantities for sale'),
			'store' => $this->l('Stores'),
			'warehouse' => $this->l('Warehouse'),
		);

		if (!$this->object->id)
			$this->fields_import_form = array(
				'legend' => array(
					'title' => $this->l('Import data from another shop')
				),
				'label' => $this->l('Import data from another shop'),
				'checkbox' => array(
					'type' => 'checkbox',
					'label' => $this->l('Duplicate data from shop'),
					'name' => 'useImportData',
					'value' => 1
				),
				'select' => array(
					'type' => 'select',
					'name' => 'importFromShop',
					'options' => array(
						'query' => Shop::getShops(false),
						'name' => 'name'
					)
				),
				'allcheckbox' => array(
					'type' => 'checkbox',
					'values' => $import_data
				),
				'desc' => $this->l('Use this option to associate data (products, modules, etc.) the same way as the selected shop')
			);

		$this->fields_value = array(
			'id_group_shop' => $obj->id_group_shop,
			'active' => true
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
	 *
	 * @param string $token
	 */
	public function processAdd($token)
	{
		/* Checking fields validity */
		$this->validateRules();

		if (!count($this->_errors))
		{
			$object = new $this->className();
			$this->copyFromPost($object, $this->table);
			$this->beforeAdd($object);
			if (!$object->add())
			{
				$this->_errors[] = Tools::displayError('An error occurred while creating object.').
					' <b>'.$this->table.' ('.Db::getInstance()->getMsgError().')</b>';
			}
			 /* voluntary do affectation here */
			else if (($_POST[$this->identifier] = $object->id) && $this->postImage($object->id) && !count($this->_errors) && $this->_redirect)
			{
				$parent_id = (int)Tools::getValue('id_parent', 1);
				$this->afterAdd($object);
				$this->updateAssoShop($object->id);
				// Save and stay on same form
				if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$object->id.'&conf=3&update'.$this->table.'&token='.$token;
				// Save and back to parent
				if (Tools::isSubmit('submitAdd'.$this->table.'AndBackToParent'))
					$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'='.$parent_id.'&conf=3&token='.$token;
				// Default behavior (save and back)
				if (empty($this->redirect_after))
					$this->redirect_after = self::$currentIndex.($parent_id ? '&'.$this->identifier.'='.$object->id : '').'&conf=3&token='.$token;
			}
		}

		$this->_errors = array_unique($this->_errors);
		if (count($this->_errors) > 0)
			return;

		$shop = new Shop($object->id);
		return $object;
	}
}
