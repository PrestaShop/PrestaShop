<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminAttributesGroupsControllerCore extends AdminController
{
	protected $id_attribute;
	protected $position_identifier = 'id_attribute_group';

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->table = 'attribute_group';
		$this->identifier = 'id_attribute_group';
		$this->className = 'AttributeGroup';
		$this->lang = true;
		$this->_defaultOrderBy = 'position';

		$this->fields_list = array(
			'id_attribute_group' => array(
				'title' => $this->l('ID'),
				'width' => 25,
				'align' => 'center'
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto',
				'filter_key' => 'b!name',
				'align' => 'left'
			),
			'count_values' => array(
				'title' => $this->l('Values count'),
				'width' => 120,
				'align' => 'center',
			),
			'position' => array(
				'title' => $this->l('Position'),
				'width' => 40,
				'filter_key' => 'a!position',
				'position' => 'position',
				'align' => 'center'
			),
		);

 		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		$this->fieldImageSettings = array('name' => 'texture', 'dir' => 'co');

		parent::__construct();
	}

	/**
	 * AdminController::renderList() override
	 * @see AdminController::renderList()
	 */
	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->addRowAction('details');

		return parent::renderList();
	}

	/**
	 * method call when ajax request is made with the details row action
	 * @see AdminController::postProcess()
	 */
	public function ajaxProcessDetails()
	{
		if (($id = Tools::getValue('id')))
		{
			$this->table = 'attribute';
			$this->className = 'Attribute';
			$this->identifier = 'id_attribute';
			$this->lang = true;

			if (!Validate::isLoadedObject($obj = new AttributeGroup((int)$id)))
				$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');

			$this->fields_list = array(
				'id_attribute' => array(
					'title' => $this->l('ID'),
					'width' => 40,
					'align' => 'center'
				),
				'name' => array(
					'title' => $this->l('Value'),
					'width' => 'auto',
					'filter_key' => 'b!name'
				)
			);

			if ($obj->group_type == 'color')
				$this->fields_list['color'] = array(
					'title' => $this->l('Color'),
					'width' => 40,
					'filter_key' => 'b!color'
				);

			$this->fields_list['position'] = array(
				'title' => $this->l('Position'),
				'width' => 40,
				'filter_key' => 'a!position',
				'position' => 'position'
			);

			$this->addRowAction('edit');
			$this->addRowAction('delete');

			// override attributes
			$this->display = 'list';
			$this->tpl_folder = 'attributes/';

			$this->_where = 'AND a.`id_attribute_group` = '.(int)$id;
			$this->_orderBy = 'position';

			// get list and force no limit clause in the request
			$this->getList($this->context->language->id, null, null, 0, false);

			// Render list
			$helper = new HelperList();
			$helper->actions = $this->actions;
			$helper->override_folder = $this->tpl_folder;
			$helper->no_link = true;
			$helper->shopLinkType = '';
			$helper->identifier = $this->identifier;
			$helper->toolbar_scroll = false;
			$helper->orderBy = 'position';
			$helper->orderWay = 'ASC';
			$helper->currentIndex = self::$currentIndex;
			$helper->token = $this->token;
			$helper->table = $this->table;
			$helper->simple_header = true;
			$helper->show_toolbar = false;
			$helper->bulk_actions = $this->bulk_actions;
			$helper->position_identifier = 'id_attribute';
			$content = $helper->generateList($this->_list, $this->fields_list);

			die (Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content)));
		}
	}

	/**
	 * AdminController::renderForm() override
	 * @see AdminController::renderForm()
	 */
	public function renderForm()
	{
		$this->table = 'attribute_group';
		$this->identifier = 'id_attribute_group';

		$group_type = array(
			array(
				'id' => 'select',
				'name' => $this->l('Drop-down list')
			),
			array(
				'id' => 'radio',
				'name' => $this->l('Radio button')
			),
			array(
				'id' => 'color',
				'name' => $this->l('Color')
			),
		);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Attributes'),
				'image' => '../img/admin/asterisk.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'lang' => true,
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Public name:'),
					'name' => 'public_name',
					'lang' => true,
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}',
					'desc' => $this->l('Group name displayed to the customer')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Attribute type:'),
					'name' => 'group_type',
					'required' => true,
					'options' => array(
						'query' => $group_type,
						'id' => 'id',
						'name' => 'name'
					),
					'desc' => $this->l('Choose the type of the attribute')
				)
			)
		);

		if (Shop::isFeatureActive())
		{
			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
			);
		}

		$this->fields_form['submit'] = array(
			'title' => $this->l('Save   '),
			'class' => 'button'
		);

		if (!($obj = $this->loadObject(true)))
			return;

		return parent::renderForm();
	}

	public function renderFormAttributes()
	{
		$attributes_groups = AttributeGroup::getAttributesGroups($this->context->language->id);

		$this->table = 'attribute';
		$this->identifier = 'id_attribute';

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Values'),
				'image' => '../img/admin/asterisk.gif',
			),
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Attribute type:'),
					'name' => 'id_attribute_group',
					'required' => true,
					'options' => array(
						'query' => $attributes_groups,
						'id' => 'id_attribute_group',
						'name' => 'name'
					),
					'desc' => $this->l('Choose the type of the attribute')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Value:'),
					'name' => 'name',
					'lang' => true,
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				)
			)
		);

		if (Shop::isFeatureActive())
		{
			// We get all associated shops for all attribute groups, because we will disable group shops
			// for attributes that the selected attribute group don't support
			$sql = 'SELECT id_attribute_group, id_shop FROM '._DB_PREFIX_.'attribute_group_shop';
			$associations = array();
			foreach (Db::getInstance()->executeS($sql) as $row)
				$associations[$row['id_attribute_group']][] = $row['id_shop'];

			$this->fields_form['input'][] = array(
				'type' => 'shop',
				'label' => $this->l('Shop association:'),
				'name' => 'checkBoxShopAsso',
				'values' => Shop::getTree()
			);
		}
		else
			$associations = array();

		$this->fields_form['shop_associations'] = Tools::jsonEncode($associations);

		$this->fields_form['input'][] = array(
			'type' => 'color',
			'label' => $this->l('Color:'),
			'name' => 'color',
			'size' => 33,
			'desc' => $this->l('Choose a color with the color picker, or enter an HTML color (e.g. "lightblue", "#CC6600")')
		);

		$this->fields_form['input'][] = array(
			'type' => 'file',
			'label' => $this->l('Texture:'),
			'name' => 'texture',
			'desc' => array(
				$this->l('Upload color texture from your computer'),
				$this->l('This will override the HTML color!')
			)
		);

		$this->fields_form['input'][] = array(
			'type' => 'current_texture',
			'label' => $this->l('Current texture:'),
			'name' => 'current_texture'
		);

		$this->fields_form['submit'] = array(
			'title' => $this->l('Save   '),
			'class' => 'button'
		);

		// Override var of Controller
		$this->table = 'attribute';
		$this->className = 'Attribute';
		$this->identifier = 'id_attribute';
		$this->lang = true;
		$this->tpl_folder = 'attributes/';

		// Create object Attribute
		if (!$obj = new Attribute((int)Tools::getValue($this->identifier)))
			return;

		$str_attributes_groups = '';
		foreach ($attributes_groups as $attribute_group)
			$str_attributes_groups .= '"'.$attribute_group['id_attribute_group'].'" : '.($attribute_group['group_type'] == 'color' ? '1' : '0'  ).', ';

		$image = '../img/'.$this->fieldImageSettings['dir'].'/'.(int)$obj->id.'.jpg';

		$this->tpl_form_vars = array(
			'strAttributesGroups' => $str_attributes_groups,
			'colorAttributeProperties' => Validate::isLoadedObject($obj) && $obj->isColorAttribute(),
			'imageTextureExists' => file_exists(_PS_IMG_DIR_.$this->fieldImageSettings['dir'].'/'.(int)$obj->id.'.jpg'),
			'imageTexture' => $image,
			'imageTextureUrl' => Tools::safeOutput($_SERVER['REQUEST_URI']).'&deleteImage=1'
		);

		return parent::renderForm();
	}

	/**
	 * AdminController::init() override
	 * @see AdminController::init()
	 */
	public function init()
	{
		if (Tools::isSubmit('updateattribute'))
			$this->display = 'editAttributes';
		else if (Tools::isSubmit('submitAddattribute'))
			$this->display = 'editAttributes';
		else if (Tools::isSubmit('submitAddattribute_group'))
			$this->display = 'add';

		parent::init();
	}

	/**
	 * Override processAdd to change SaveAndStay button action
	 * @see classes/AdminControllerCore::processUpdate()
	 */
	public function processAdd()
	{
		$object = parent::processAdd();

		if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && !count($this->errors))
		{
			if ($this->display == 'add')
				$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&conf=3&update'.$this->table.'&token='.$this->token;
			else
				$this->redirect_after = self::$currentIndex.'&id_attribute_group='.(int)Tools::getValue('id_attribute_group').'&conf=3&update'.$this->table.'&token='.$this->token;
		}
		
		if (count($this->errors))
			$this->setTypeAttribute();
		
		return $object;
	}

	/**
	 * Override processUpdate to change SaveAndStay button action
	 * @see classes/AdminControllerCore::processUpdate()
	 */
	public function processUpdate()
	{
		$object = parent::processUpdate();

		if (Tools::isSubmit('submitAdd'.$this->table.'AndStay') && !count($this->errors))
		{
			if ($this->display == 'add')
				$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&conf=3&update'.$this->table.'&token='.$this->token;
			else
				$this->redirect_after = self::$currentIndex.'&'.$this->identifier.'=&id_attribute_group='.(int)Tools::getValue('id_attribute_group').'&conf=3&update'.$this->table.'&token='.$this->token;
		}
		
		if (count($this->errors))
			$this->setTypeAttribute();
			
		return $object;
	}

	/**
	 * AdminController::initContent() override
	 * @see AdminController::initContent()
	 */
	public function initContent()
	{
		if (!Combination::isFeatureActive())
		{
			$this->displayWarning($this->l('This feature has been disabled. You can activate it at:').
				' <a href="index.php?tab=AdminPerformance&token='.Tools::getAdminTokenLite('AdminPerformance').
				'#featuresDetachables">'.$this->l('Performances').'</a>');
			return;
		}

		// toolbar (save, cancel, new, ..)
		$this->initToolbar();
		if ($this->display == 'edit' || $this->display == 'add')
		{
			if (!($this->object = $this->loadObject(true)))
				return;
			$this->content .= $this->renderForm();
		}
		else if ($this->display == 'editAttributes')
		{
			if (!$this->object = new Attribute((int)Tools::getValue('id_attribute')))
				return;

			$this->content .= $this->renderFormAttributes();
		}
		else if ($this->display != 'view' && !$this->ajax)
		{
			$this->content .= $this->renderList();
			$this->content .= $this->renderOptions();
		}

		$this->context->smarty->assign(array(
			'table' => $this->table,
			'current' => self::$currentIndex,
			'token' => $this->token,
			'content' => $this->content,
			'url_post' => self::$currentIndex.'&token='.$this->token,
		));
	}

	public function initToolbar()
	{
		switch ($this->display)
		{
			// @todo defining default buttons
			case 'add':
			case 'edit':
			case 'editAttributes':
				// Default save button - action dynamically handled in javascript
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);

				if ($this->display == 'editAttributes' && !$this->id_attribute)
					$this->toolbar_btn['save-and-stay'] = array(
						'short' => 'SaveAndStay',
						'href' => '#',
						'desc' => $this->l('Save then add another value'),
						'force_desc' => true,
					);

				$back = self::$currentIndex.'&token='.$this->token;
				$this->toolbar_btn['back'] = array(
					'href' => $back,
					'desc' => $this->l('Back to list')
				);
				break;
			default: // list
				$this->toolbar_btn['new'] = array(
					'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token,
					'desc' => $this->l('Add New Attributes')
				);
				$this->toolbar_btn['newAttributes'] = array(
					'href' => self::$currentIndex.'&amp;updateattribute&amp;token='.$this->token,
					'desc' => $this->l('Add New Values'),
					'class' => 'toolbar-new'
				);
		}
	}

	public function initToolbarTitle()
	{
		$bread_extended = $this->breadcrumbs;

		switch ($this->display)
		{
			case 'edit':
				$bread_extended[] = $this->l('Edit New Attributes');
				break;

			case 'add':
				$bread_extended[] = $this->l('Add New Attributes');
				break;

			case 'editAttributes':
				if ($this->id_attribute)
					$bread_extended[] = $this->l('Edit Value');
				else
					$bread_extended[] = $this->l('Add New Values');
				break;
		}

		$this->toolbar_title = $bread_extended;
	}

	public function initProcess()
	{
		$this->setTypeAttribute();

		parent::initProcess();

		if ($this->table == 'attribute')
		{
			$this->display = 'editAttributes';
			$this->id_attribute = (int)Tools::getValue('id_attribute');
		}
	}
	
	protected function setTypeAttribute()
	{
		if (Tools::getValue('updateattribute') || Tools::isSubmit('deleteattribute') || Tools::isSubmit('submitAddattribute') || Tools::isSubmit('submitBulkdeleteattribute'))
		{
			$this->table = 'attribute';
			$this->className = 'Attribute';
			$this->identifier = 'id_attribute';
			
			if ($this->display == 'edit')
				$this->display = 'editAttributes';
		}
	}

	/**
	 * Call the right method for creating or updating object
	 *
	 * @return mixed
	 */
	public function processSave()
	{
		if ($this->display == 'add' || $this->display == 'edit')
			$this->identifier = 'id_attribute_group';

		if (!$this->id_object)
			return $this->processAdd();
		else
			return $this->processUpdate();
	}

	public function postProcess()
	{
		if (!Combination::isFeatureActive())
			return;

		if (!Tools::getValue($this->identifier) && Tools::getValue('id_attribute') && !Tools::getValue('attributeOrderby'))
		{
			// Override var of Controller
			$this->table = 'attribute';
			$this->className = 'Attribute';
			$this->identifier = 'id_attribute';
		}

		// If it's an attribute, load object Attribute()
		if (Tools::getValue('updateattribute') || Tools::isSubmit('deleteattribute') || Tools::isSubmit('submitAddattribute'))
		{
			if ($this->tabAccess['edit'] !== '1')
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
			else if (!$object = new Attribute((int)Tools::getValue($this->identifier)))
				$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');

			if (Tools::getValue('position') !== false && Tools::getValue('id_attribute'))
			{
				$_POST['id_attribute_group'] = $object->id_attribute_group;
				if (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
					$this->errors[] = Tools::displayError('Failed to update the position.');
				else
					Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.Tools::getAdminTokenLite('AdminAttributesGroups').'#details_details_'.$object->id_attribute_group);
			}
			else if (Tools::isSubmit('deleteattribute') && Tools::getValue('id_attribute'))
			{
				if (!$object->delete())
					$this->errors[] = Tools::displayError('Failed to delete the attribute.');
				else
					Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.Tools::getAdminTokenLite('AdminAttributesGroups'));
			}
			else if (Tools::isSubmit('submitAddattribute'))
			{
				Hook::exec('actionObjectAttributeAddBefore');
				$this->action = 'save';
				$id_attribute = (int)Tools::getValue('id_attribute');
				// Adding last position to the attribute if not exist
				if ($id_attribute <= 0)
				{
					$sql = 'SELECT `position`+1
							FROM `'._DB_PREFIX_.'attribute`
							WHERE `id_attribute_group` = '.(int)Tools::getValue('id_attribute_group').'
							ORDER BY position DESC';
					// set the position of the new group attribute in $_POST for postProcess() method
					$_POST['position'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
				}
				$_POST['id_parent'] = 0;
				$this->processSave($this->token);
			}
		}
		else
		{
			if (Tools::getValue('submitDel'.$this->table))
			{
				if ($this->tabAccess['delete'] === '1')
				{
					if (isset($_POST[$this->table.'Box']))
					{
						$object = new $this->className();
						if ($object->deleteSelection($_POST[$this->table.'Box']))
							Tools::redirectAdmin(self::$currentIndex.'&conf=2'.'&token='.$this->token);
						$this->errors[] = Tools::displayError('An error occurred while deleting this selection.');
					}
					else
						$this->errors[] = Tools::displayError('You must select at least one element to delete.');
				}
				else
					$this->errors[] = Tools::displayError('You do not have permission to delete this.');
				// clean position after delete
				AttributeGroup::cleanPositions();
			}
			else if (Tools::isSubmit('submitAdd'.$this->table))
			{
				Hook::exec('actionObjectAttributeGroupAddBefore');
				$id_attribute_group = (int)Tools::getValue('id_attribute_group');
				// Adding last position to the attribute if not exist
				if ($id_attribute_group <= 0)
				{
					$sql = 'SELECT `position`+1
							FROM `'._DB_PREFIX_.'attribute_group`
							ORDER BY position DESC';
				// set the position of the new group attribute in $_POST for postProcess() method
					$_POST['position'] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
				}
				// clean \n\r characters
				foreach ($_POST as $key => $value)
					if (preg_match('/^name_/Ui', $key))
						$_POST[$key] = str_replace ('\n', '', str_replace('\r', '', $value));
				parent::postProcess();
			}
			else
				parent::postProcess();
		}
	}

	/**
	 * AdminController::getList() override
	 * @see AdminController::getList()
	 */
	public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
	{
		parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);

		if ($this->ajax)
		{
			foreach ($this->_list as &$list)
				if (file_exists(_PS_IMG_DIR_.$this->fieldImageSettings['dir'].'/'.(int)$list['id_attribute'].'.jpg'))
					$list['color']['texture'] = '../img/'.$this->fieldImageSettings['dir'].'/'.(int)$list['id_attribute'].'.jpg';
		}
		else
		{
			$nb_items = count($this->_list);
			for ($i = 0; $i < $nb_items; ++$i)
			{
				$item = &$this->_list[$i];

				$query = new DbQuery();
				$query->select('COUNT(a.id_attribute) as count_values');
				$query->from('attribute', 'a');
				$query->join(Shop::addSqlAssociation('attribute', 'a'));
				$query->where('a.id_attribute_group ='.(int)$item['id_attribute_group']);
				$query->groupBy('attribute_shop.id_shop');
				$query->orderBy('count_values DESC');
				$item['count_values'] = (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
				unset($query);
			}
		}
	}

	/**
	 * Overrides parent to delete items from sublist
	 *
	 * @return mixed
	 */
	public function processBulkDelete()
	{
		// If we are deleting attributes instead of attribute_groups
		if (Tools::getIsset('attributeBox'))
		{
			$this->className = 'Attribute';
			$this->table = 'attribute';
			$this->boxes = Tools::getValue($this->table.'Box');
		}

		$result = parent::processBulkDelete();
		// Restore vars
		$this->className = 'AttributeGroup';
		$this->table = 'attribute_group';

		return $result;
	}

	/* Modify group attribute position */
	public function ajaxProcessUpdateGroupsPositions()
	{
		$way = (int)Tools::getValue('way');
		$id_attribute_group = (int)Tools::getValue('id_attribute_group');
		$positions = Tools::getValue('attribute_group');

		$new_positions = array();
		foreach ($positions as $k => $v)
			if (count(explode('_', $v)) == 4)
				$new_positions[] = $v;

		foreach ($new_positions as $position => $value)
		{
			$pos = explode('_', $value);

			if (isset($pos[2]) && (int)$pos[2] === $id_attribute_group)
			{
				if ($group_attribute = new AttributeGroup((int)$pos[2]))
					if (isset($position) && $group_attribute->updatePosition($way, $position))
						echo 'ok position '.(int)$position.' for group attribute '.(int)$pos[2].'\r\n';
					else
						echo '{"hasError" : true, "errors" : "Can not update group attribute '.(int)$id_attribute_group.' to position '.(int)$position.' "}';
				else
					echo '{"hasError" : true, "errors" : "This group attribute ('.(int)$id_attribute_group.') can t be loaded"}';

				break;
			}
		}
	}

	/* Modify attribute position */
	public function ajaxProcessUpdateAttributesPositions()
	{
		$way = (int)Tools::getValue('way');
		$id_attribute = (int)Tools::getValue('id_attribute');
		$id_attribute_group = (int)Tools::getValue('id_attribute_group');
		$positions = Tools::getValue('attribute');

		if (is_array($positions))
			foreach ($positions as $position => $value)
			{
				$pos = explode('_', $value);

				if ((isset($pos[1]) && isset($pos[2])) && ($pos[1] == $id_attribute_group && (int)$pos[2] === $id_attribute))
				{
					if ($attribute = new Attribute((int)$pos[2]))
						if (isset($position) && $attribute->updatePosition($way, $position))
							echo 'ok position '.(int)$position.' for attribute '.(int)$pos[2].'\r\n';
						else
							echo '{"hasError" : true, "errors" : "Can not update attribute '.(int)$id_attribute.' to position '.(int)$position.' "}';
					else
						echo '{"hasError" : true, "errors" : "This attribute ('.(int)$id_attribute.') can t be loaded"}';

					break;
				}
			}

	}
}
