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

class AdminCategoriesControllerCore extends AdminController
{
	/**
	 *  @var object Category() instance for navigation
	 */
	private $_category = null;

	public function __construct()
	{
		$this->table = 'category';
		$this->className = 'Category';
		$this->lang = true;
		$this->deleted = false;

		$this->context = Context::getContext();

 		$this->fieldImageSettings = array(
 			'name' => 'image',
 			'dir' => 'c'
 		);

		$this->fieldsDisplay = array(
			'id_category' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 20
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto'
			),
			'description' => array(
				'title' => $this->l('Description'),
				'width' => 500,
				'maxlength' => 90,
				'callback' => 'getDescriptionClean',
				'orderby' => false
			),
			'position' => array(
				'title' => $this->l('Position'),
				'width' => 40,
				'filter_key' => 'cs!position',
				'align' => 'center',
				'position' => 'position'
			),
			'active' => array(
				'title' => $this->l('Displayed'),
				'active' => 'status',
				'align' => 'center',
				'type' => 'bool',
				'width' => 70,
				'orderby' => false
			)
		);

		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected')));
		$this->specificConfirmDelete = false;
		
		parent::__construct();
	}

	public function init()
	{
		parent::init();

		// context->shop is set in the init() function, so we move the _category instanciation after that
		if (($id_category = Tools::getvalue('id_category')) && $this->action != 'select_delete')
			$this->_category = new Category($id_category);
		else
			if (Shop::isFeatureActive() && Shop::getContext() == Shop::CONTEXT_SHOP)
				$this->_category = new Category($this->context->shop->id_category);
			else if (count(Category::getCategoriesWithoutParent()) > 1)
				$this->_category = Category::getTopCategory();
			else
				$this->_category = new Category(Configuration::get('PS_HOME_CATEGORY'));

		// if we are not in a shop context, we remove the position column
		if (Shop::getContext() != Shop::CONTEXT_SHOP)
			unset($this->fieldsDisplay['position']);
		// shop restriction : if category is not available for current shop, we redirect to the list from default category
		if (!Shop::isCategoryAvailable($this->_category->id))
		{
			$this->redirect_after = self::$currentIndex.'&id_category='.(int)$this->context->shop->getCategory().'&token='.$this->token;
			$this->redirect();
		}
	}
	
	public function initContent()
	{
		if ($this->action == 'select_delete')
			$this->context->smarty->assign(array(
				'delete_form' => true,
				'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
				'boxes' => $this->boxes,
			));

		parent::initContent();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('add');
		$this->addRowAction('view');

		$count_categories_without_parent = count(Category::getCategoriesWithoutParent());
		$is_multishop = Shop::isFeatureActive();
		$top_category = Category::getTopCategory();
		if (Tools::isSubmit('id_category'))
			$id_parent = $this->_category->id;
		else if (!$is_multishop && $count_categories_without_parent > 1)
			$id_parent = $top_category->id;
		else if ($is_multishop && $count_categories_without_parent == 1)
			$id_parent = Configuration::get('PS_HOME_CATEGORY');
		else if ($is_multishop && $count_categories_without_parent > 1 && Shop::getContext() != Shop::CONTEXT_SHOP)
			$id_parent = $top_category->id;
		else
			$id_parent = $this->context->shop->id_category;

		$this->_filter .= ' AND `id_parent` = '.(int)$id_parent.' ';
		$this->_select = 'cs.`position` ';
		$id = $this->context->shop->id;
		$id_shop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');
		$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'category_shop` cs ON (a.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')';
		// we add restriction for shop
		if (Shop::getContext() == Shop::CONTEXT_SHOP && $is_multishop)
			$this->_where = ' AND cs.`id_shop` = '.(int)Context::getContext()->shop->id;

		$categories_tree = $this->_category->getParentsCategories();
		if (empty($categories_tree)
			&& ($this->_category->id_category != 1 || Tools::isSubmit('id_category'))
			&& (Shop::getContext() == Shop::CONTEXT_SHOP && !$is_multishop && $count_categories_without_parent > 1))
			$categories_tree = array(array('name' => $this->_category->name[$this->context->language->id]));

		asort($categories_tree);
		$this->tpl_list_vars['categories_tree'] = $categories_tree;

		if (Tools::isSubmit('submitBulkdelete'.$this->table) || Tools::isSubmit('delete'.$this->table))
		{
			$category = new Category(Tools::getValue('id_category'));
			if ($category->is_root_category)
				$this->tpl_list_vars['need_delete_mode'] = false;
			else
				$this->tpl_list_vars['need_delete_mode'] = true;
			$this->tpl_list_vars['delete_category'] = true;
			$this->tpl_list_vars['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
			$this->tpl_list_vars['POST'] = $_POST;
		}
		
		return parent::renderList();
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, 'cs.position', $order_way, $start, $limit, Context::getContext()->shop->id);
		// Check each row to see if there are combinations and get the correct action in consequence

		$nb_items = count($this->_list);
		for ($i = 0; $i < $nb_items; $i++)
		{
			$item = &$this->_list[$i];
			$category_tree = Category::getChildren((int)$item['id_category'], $this->context->language->id);
			if (!count($category_tree))
				$this->addRowActionSkipList('view', array($item['id_category']));
		}
	}

	public function renderView()
	{
		$this->initToolbar();
		return $this->renderList();
	}

	public function initToolbar()
	{
		if (empty($this->display))
		{
			if (Shop::isFeatureActive())
				$this->toolbar_btn['new-url'] = array(
					'href' => self::$currentIndex.'&amp;add'.$this->table.'root&amp;token='.$this->token,
					'desc' => $this->l('Add new root category')
				);
			$this->toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token,
				'desc' => $this->l('Add new')
			);
		}
		// be able to edit the Home category
		if (count(Category::getCategoriesWithoutParent()) == 1 && !Tools::isSubmit('id_category'))
			$this->toolbar_btn['edit'] = array(
				'href' => self::$currentIndex.'&amp;update'.$this->table.'&amp;id_category='.(int)$this->_category->id.'&amp;token='.$this->token,
				'desc' => $this->l('Edit')
			);
		if (Tools::getValue('id_category') && !Tools::isSubmit('updatecategory'))
		{
			$this->toolbar_btn['edit'] = array(
				'href' => self::$currentIndex.'&amp;update'.$this->table.'&amp;id_category='.(int)Tools::getValue('id_category').'&amp;token='.$this->token,
				'desc' => $this->l('Edit')
			);
			$back = Tools::safeOutput(Tools::getValue('back', ''));
			if (empty($back))
				$back = self::$currentIndex.'&token='.$this->token;
			$this->toolbar_btn['cancel'] = array(
				'href' => $back,
				'desc' => $this->l('Cancel')
			);
		}
		if ($this->display == 'view')
			$this->toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;id_parent='.(int)Tools::getValue('id_category').'&amp;token='.$this->token,
				'desc' => $this->l('Add new')
			);
		parent::initToolbar();
		if ($this->_category->id == Category::getTopCategory()->id && isset($this->toolbar_btn['new']))
			unset($this->toolbar_btn['new']);
		// after adding a category
		if (empty($this->display))
		{
			$id_category = (Tools::isSubmit('id_category')) ? '&amp;id_parent='.(int)Tools::getValue('id_category') : '';
			$this->toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token.$id_category,
				'desc' => $this->l('Add new')
			);
		}
	}

	public function initProcess()
	{
		if (Tools::isSubmit('add'.$this->table.'root'))
		{
			if ($this->tabAccess['add'])
			{
				$this->action = 'add'.$this->table.'root';
				$this->display = 'edit';
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}

		parent::initProcess();

		if ($this->action == 'delete' || $this->action == 'bulkdelete')
			if (Tools::getIsset('cancel'))
				Tools::redirectAdmin(self::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminCategories'));
			elseif (Tools::getValue('deleteMode') == 'link' || Tools::getValue('deleteMode') == 'linkanddisable' || Tools::getValue('deleteMode') == 'delete')
				$this->delete_mode = Tools::getValue('deleteMode');
			else
				$this->action = 'select_delete';
	}

	public function renderForm()
	{
		$this->initToolbar();
		$obj = $this->loadObject(true);
		$id_shop = Context::getContext()->shop->id;
		$selected_cat = array((isset($obj->id_parent) && $obj->isParentCategoryAvailable($id_shop))? $obj->id_parent : Tools::getValue('id_parent', Category::getRootCategory()->id));
		$unidentified = new Group(Configuration::get('PS_UNIDENTIFIED_GROUP'));
		$guest = new Group(Configuration::get('PS_GUEST_GROUP'));
		$default = new Group(Configuration::get('PS_CUSTOMER_GROUP'));

		$unidentified_group_information = sprintf($this->l('%s - All persons without a customer account or unauthenticated.'), '<b>'.$unidentified->name[$this->context->language->id].'</b>');
		$guest_group_information = sprintf($this->l('%s - Customer who placed an order with the Guest Checkout.'), '<b>'.$guest->name[$this->context->language->id].'</b>');
		$default_group_information = sprintf($this->l('%s - All persons who created an account on this site.'), '<b>'.$default->name[$this->context->language->id].'</b>');
		$root_category = Category::getRootCategory();
		$root_category = array('id_category' => $root_category->id_category, 'name' => $root_category->name);
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Category'),
				'image' => '../img/admin/tab-categories.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'lang' => true,
					'size' => 48,
					'required' => true,
					'class' => 'copy2friendlyUrl',
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Displayed:'),
					'name' => 'active',
					'required' => false,
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
					)
				),
				array(
					'type' => 'categories',
					'label' => $this->l('Parent category:'),
					'name' => 'id_parent',
					'values' => array(
						'trads' => array(
							 'Root' => $root_category,
							 'selected' => $this->l('selected'),
							 'Collapse All' => $this->l('Collapse All'),
							 'Expand All' => $this->l('Expand All')
						),
						'selected_cat' => $selected_cat,
						'input_name' => 'id_parent',
						'use_radio' => true,
						'use_search' => false,
						'disabled_categories' => array(4),
						'top_category' => Category::getTopCategory(),
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Root Category:'),
					'name' => 'is_root_category',
					'required' => false,
					'is_bool' => true,
					'class' => 't',
					'values' => array(
						array(
							'id' => 'is_root_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'is_root_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					)
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'lang' => true,
					'rows' => 10,
					'cols' => 100,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'file',
					'label' => $this->l('Image:'),
					'name' => 'image',
					'display_image' => true,
					'desc' => $this->l('Upload category logo from your computer')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta title:'),
					'name' => 'meta_title',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Meta description:'),
					'name' => 'meta_description',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'tags',
					'label' => $this->l('Meta keywords:'),
					'name' => 'meta_keywords',
					'lang' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Friendly URL:'),
					'name' => 'link_rewrite',
					'lang' => true,
					'required' => true,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}'
				),
				array(
					'type' => 'group',
					'label' => $this->l('Group access:'),
					'name' => 'groupBox',
					'values' => Group::getGroups(Context::getContext()->language->id),
					'info_introduction' => $this->l('You have now three default customer groups.'),
					'unidentified' => $unidentified_group_information,
					'guest' => $guest_group_information,
					'customer' => $default_group_information,
					'desc' => $this->l('Mark all groups you want to give access to this category')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);
		if (Shop::isFeatureActive())
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
			);
		// remove category tree and radio button "is_root_category" if this category has the root category as parent category to avoid any conflict
		if ($this->_category->id_parent == Category::getTopCategory()->id && Tools::isSubmit('updatecategory'))
			foreach ($this->fields_form['input'] as $k => $input)
				if (in_array($input['name'], array('id_parent', 'is_root_category')))
					unset($this->fields_form['input'][$k]);

		if (Tools::isSubmit('add'.$this->table.'root'))
			unset($this->fields_form['input'][2],$this->fields_form['input'][3]);

		if (!($obj = $this->loadObject(true)))
			return;

		$image = ImageManager::thumbnail(_PS_CAT_IMG_DIR_.'/'.$obj->id.'.jpg', $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350, $this->imageType, true);

		$this->fields_value = array(
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_CAT_IMG_DIR_.'/'.$obj->id.'.jpg') / 1000 : false
		);

		// Added values of object Group
		$category_groups = $obj->getGroups();
		$category_groups_ids = array();
		if (is_array($category_groups))
			foreach ($category_groups as $category_group)
				$category_groups_ids[] = $category_group['id_group'];

		$groups = Group::getGroups($this->context->language->id);
		// if empty $carrier_groups_ids : object creation : we set the default groups
		if (empty($category_groups_ids))
		{
			$preselected = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP'));
			$category_groups_ids = array_merge($category_groups_ids, $preselected);
		}
		foreach ($groups as $group)
			$this->fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $category_groups_ids)));

		return parent::renderForm();
	}

	public function processAdd($token)
	{
		$id_category = (int)Tools::getValue('id_category');
		$id_parent = (int)Tools::getValue('id_parent');
		// if true, we are in a root category creation
		if (!$id_parent && !Tools::isSubmit('is_root_category'))
			$_POST['is_root_category'] = $_POST['level_depth'] = $_POST['id_parent'] = $id_parent = 1;
		if ($id_category)
		{
			if ($id_category != $id_parent)
			{
				if (!Category::checkBeforeMove($id_category, $id_parent))
					$this->errors[] = Tools::displayError($this->l('Category cannot be moved here'));
			}
			else
				$this->errors[] = Tools::displayError($this->l('Category cannot be parent of herself.'));
		}
		parent::processAdd($token);
	}

	public function processDelete($token)
	{
		if ($this->tabAccess['delete'] === '1')
		{
			if ($this->delete_mode == 'link' || $this->delete_mode == 'linkanddisable')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					$object->remove_products = false;
					if ($this->delete_mode == 'linkanddisable')
						$object->disable_products = true;
				}
			}
			else if ($this->delete_mode != 'delete')
			{
				$this->errors[] = Tools::displayError('Unknown delete mode:'.' '.$this->deleted);
				return;
			}
		
			if (Tools::isSubmit($this->table.'Box'))
			{
				if (isset($_POST[$this->table.'Box']))
				{
					$category = new Category();
					$result = true;
					$result = $category->deleteSelection(Tools::getValue($this->table.'Box'));
					if ($result)
					{
						$category->cleanPositions((int)Tools::getValue('id_category'));
						Tools::redirectAdmin(self::$currentIndex.'&conf=2&token='.Tools::getAdminTokenLite('AdminCategories').'&id_category='.(int)Tools::getValue('id_category'));
					}
					$this->errors[] = Tools::displayError('An error occurred while deleting selection.');
				}
				else
					$this->errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
			{
				if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
				{
					if ($object->isRootCategoryForAShop())
						$this->errors[] = Tools::displayError('You cannot remove this category because a shop uses this category as a root category.');
					// check if request at least one object with noZeroObject
					elseif (isset($object->noZeroObject) &&
						count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
						$this->errors[] = Tools::displayError('You need at least one object.').' <b>'.
							$this->table.'</b><br />'.Tools::displayError('You cannot delete all of the items.');
					else
					{
						if ($this->deleted)
						{
							$object->deleteImage();
							$object->deleted = 1;
							if ($object->update())
								Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token').'&id_category='.(int)$object->id_parent);
						}
						else if ($object->delete())
							Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getValue('token').'&id_category='.(int)$object->id_parent);
						$this->errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.
						$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
		}
		else
			$this->errors[] = Tools::displayError('You do not have permission to delete here.');
	}

	public function processPosition($token)
	{
		if ($this->tabAccess['edit'] !== '1')
			$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		else if (!Validate::isLoadedObject($object = new Category((int)Tools::getValue($this->identifier, Tools::getValue('id_category_to_move', 1)))))
			$this->errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.
				$this->table.'</b> '.Tools::displayError('(cannot load object)');
		if (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
			$this->errors[] = Tools::displayError('Failed to update the position.');
		else
		{
			$object->regenerateEntireNtree();
			Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.(($id_category = (int)Tools::getValue($this->identifier, Tools::getValue('id_category_parent', 1))) ? ('&'.$this->identifier.'='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminCategories'));
		}
	}

	protected function postImage($id)
	{
		$ret = parent::postImage($id);
		if (($id_category = (int)Tools::getValue('id_category')) &&
			isset($_FILES) && count($_FILES) && $_FILES['image']['name'] != null &&
			file_exists(_PS_CAT_IMG_DIR_.$id_category.'.jpg'))
		{
			$images_types = ImageType::getImagesTypes('categories');
			foreach ($images_types as $k => $image_type)
			{
				ImageManager::resize(
					_PS_CAT_IMG_DIR_.$id_category.'.jpg',
					_PS_CAT_IMG_DIR_.$id_category.'-'.stripslashes($image_type['name']).'.jpg',
					(int)$image_type['width'], (int)$image_type['height']
				);
			}
		}

		return $ret;
	}

	/**
	  * Allows to display the category description without HTML tags and slashes
	  *
	  * @return string
	  */
	public static function getDescriptionClean($description)
	{
		return strip_tags(stripslashes($description));
	}

	protected function updateAssoShop($id_object = false, $new_id_object = false)
	{
		if (!Shop::isFeatureActive())
			return;

		$assos_data = $this->getAssoShop($this->table, $id_object);
		$assos = $assos_data[0];
		$type = $assos_data[1];

		$categories_shop = Category::getShopsByCategory($id_object);

		if (!$type)
			return;

		$delete = $insert = '';
		foreach ($assos as $asso)
		{
			$passed = false;
			$delete .= (int)$asso['id_'.$type].',';
			foreach ($categories_shop as $cat)
				if ($cat['id_shop'] == $asso['id_'.$type])
					$passed = true;
			if (!$passed)
				$insert .= '('.($new_id_object ? (int)$new_id_object : (int)$asso['id_object']).', '.(int)$asso['id_'.$type].'),';
		}
		$delete = substr($delete, 0, strlen($delete) - 1);
		$insert = substr($insert, 0, strlen($insert) - 1);
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.$this->table.'_'.$type.
			($id_object ? ' WHERE `'.$this->identifier.'` = '.(int)$id_object.' AND `id_'.$type.'` NOT IN ('.$delete.')' : ''));

		if (!empty($insert))
			Db::getInstance()->execute('
				INSERT INTO '._DB_PREFIX_.$this->table.'_'.$type.' (`'.pSQL($this->identifier).'`, `id_'.$type.'`)
				VALUES '.pSQL($insert));
	}

	/**
	 * @param $token
	 * @return bool
	 */
	protected function processSelectDelete($token)
	{
		return parent::processBulkDelete($token);
	}
}


