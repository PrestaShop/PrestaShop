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
				'filter_key' => 'position',
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

		if ($id_category = Tools::getvalue('id_category'))
			$this->_category = new Category($id_category);
		else
			$this->_category = new Category(1);

		parent::__construct();
	}
	
	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryUi('ui.widget');
		$this->addJqueryPlugin('tagify');
	}

	public function initList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('add');
		$this->addRowAction('view');

	 	$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->_filter .= ' AND `id_parent` = '.(int)$this->_category->id.' ';
		$this->_select = 'position ';

		$categories_tree = $this->_category->getParentsCategories();
		asort($categories_tree);
		$categories_name = stripslashes($this->_category->getName());
		$this->tpl_list_vars['categories_tree'] = $categories_tree;
		$this->tpl_list_vars['categories_name'] = $categories_name;

		return parent::initList();
	}

	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, 'position', $order_way, $start, $limit, Context::getContext()->shop->getID(true));
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

	public function initView()
	{
		$this->initToolbar();
		return $this->initList();
	}

	public function initToolbar()
	{
		if (empty($this->display))
			$this->toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token,
				'desc' => $this->l('Add new')
			);
		if (Tools::getValue('id_category') && !Tools::isSubmit('updatecategory'))
		{
			$this->toolbar_btn['edit'] = array(
				'href' => self::$currentIndex.'&amp;update'.$this->table.'&amp;id_category='.Tools::getValue('id_category').'&amp;token='.$this->token,
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
				'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;id_parent='.Tools::getValue('id_category').'&amp;token='.$this->token,
				'desc' => $this->l('Add new')
			);
		parent::initToolbar();
	}

	public function initForm()
	{
		$this->initToolbar();
		$obj = $this->loadObject(true);
		$selected_cat = array(isset($obj->id_parent) ? $obj->id_parent : Tools::getValue('id_parent', 1));
		if (count($selected_cat) > 0)
		{
			if (isset($selected_cat[0]))
				$selected_cat = implode(',', $selected_cat);
			else
				$selected_cat = implode(',', array_keys($selected_cat));
		}
		else
			$selected_cat = '';

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
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
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
							 'Home' => $this->l('Home'),
							 'selected' => $this->l('selected'),
							 'Collapse All' => $this->l('Collapse All'),
							 'Expand All' => $this->l('Expand All')
						),
						'selected_cat' => $selected_cat,
						'input_name' => 'id_parent',
						'use_radio' => true,
						'use_search' => false,
						'disabled_categories' => array(4),
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
					'desc' => $this->l('Mark all groups you want to give access to this category')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		if (!($obj = $this->loadObject(true)))
			return;

		$image = cacheImage(_PS_CAT_IMG_DIR_.'/'.$obj->id.'.jpg', $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350, $this->imageType, true);

		$this->fields_value = array(
			'image' => $image ? $image : false,
			'size' => $image ? filesize(_PS_CAT_IMG_DIR_.'/'.$obj->id.'.jpg') / 1000 : false
		);

		// Added values of object Group
		$carrier_groups = $obj->getGroups();
		$carrier_groups_ids = array();
		if (is_array($carrier_groups))
			foreach ($carrier_groups as $carrier_group)
				$carrier_groups_ids[] = $carrier_group['id_group'];

		$groups = Group::getGroups($this->context->language->id);
		foreach ($groups as $group)
			$this->fields_value['groupBox_'.$group['id_group']] = Tools::getValue('groupBox_'.$group['id_group'], (in_array($group['id_group'], $carrier_groups_ids)));

		return parent::initForm();
	}

	public function postProcess()
	{
		$this->tabAccess = Profile::getProfileAccess($this->context->employee->id_profile, $this->id);

		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			if ($id_category = (int)Tools::getValue('id_category'))
			{
				if (!Category::checkBeforeMove($id_category, $this->_category->id_parent))
				{
					$this->_errors[] = Tools::displayError('Category cannot be moved here');
					return false;
				}
			}
		}
		/* Delete object */
		else if (isset($_GET['delete'.$this->table]))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()) && isset($this->fieldImageSettings))
				{
					// check if request at least one object with noZeroObject
					if (isset($object->noZeroObject) &&
						count($taxes = call_user_func(array($this->className, $object->noZeroObject))) <= 1)
						$this->_errors[] = Tools::displayError('You need at least one object.').' <b>'.
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
						$this->_errors[] = Tools::displayError('An error occurred during deletion.');
					}
				}
				else
					$this->_errors[] = Tools::displayError('An error occurred while deleting object.').' <b>'.
						$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else if (isset($_GET['position']))
		{
			if ($this->tabAccess['edit'] !== '1')
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
			else if (!Validate::isLoadedObject($object = new Category((int)Tools::getValue($this->identifier, Tools::getValue('id_category_to_move', 1)))))
				$this->_errors[] = Tools::displayError('An error occurred while updating status for object.').' <b>'.
					$this->table.'</b> '.Tools::displayError('(cannot load object)');
			if (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
				$this->_errors[] = Tools::displayError('Failed to update the position.');
			else
				Tools::redirectAdmin(self::$currentIndex.'&'.$this->table.'Orderby=position&'.$this->table.'Orderway=asc&conf=5'.(($id_category = (int)Tools::getValue($this->identifier, Tools::getValue('id_category_parent', 1))) ? ('&'.$this->identifier.'='.$id_category) : '').'&token='.Tools::getAdminTokenLite('AdminCategories'));
		}
		/* Delete multiple objects */
		else if (Tools::getValue('submitDel'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
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
					$this->_errors[] = Tools::displayError('An error occurred while deleting selection.');

				}
				else
					$this->_errors[] = Tools::displayError('You must select at least one element to delete.');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to delete here.');
			return;
		}
		parent::postProcess();
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
				imageResize(
					_PS_CAT_IMG_DIR_.$id_category.'.jpg',
					_PS_CAT_IMG_DIR_.$id_category.'-'.stripslashes($image_type['name']).'.jpg',
					(int)$image_type['width'], (int)$image_type['height']
				);
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
}


