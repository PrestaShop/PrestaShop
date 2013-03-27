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

class AdminTabsControllerCore extends AdminController
{
	protected $position_identifier = 'id_tab';

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->multishop_context = Shop::CONTEXT_ALL;
		$this->table = 'tab';
		$this->className = 'Tab';
		$this->lang = true;

		$this->fieldImageSettings = array(
			'name' => 'icon',
			'dir' => 't'
		);

		$this->imageType = 'gif';

		$this->fields_list = array(
			'id_tab' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 200
			),
			'logo' => array(
				'title' => $this->l('Icon'),
				'image' => 't',
				'image_id' => 'class_name',
				'orderby' => false,
				'search' => false
			),
			'module' => array(
				'title' => $this->l('Module')
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'width' => 70,
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false
 			),
			'position' => array(
				'title' => $this->l('Position'),
				'width' => 40,
				'filter_key' => 'a!position',
				'position' => 'position'
			)
		);

		parent::__construct();
	}

	/**
	 * AdminController::renderForm() override
	 * @see AdminController::renderForm()
	 */
	public function renderForm()
	{
		$tabs = Tab::getTabs($this->context->language->id, 0);
		// If editing, we clean itself
		if (Tools::isSubmit('id_tab'))
			foreach ($tabs as $key => $tab)
				if ($tab['id_tab'] == Tools::getValue('id_tab'))
					unset($tabs[$key]);

		// added category "Home" in var $tabs
		$tab_zero = array(
			'id_tab' => 0,
			'name' => $this->l('Home')
		);
		array_unshift($tabs, $tab_zero);

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Menus'),
				'image' => '../img/admin/tab.gif'
			),
			'input' => array(
				array(
					'type' => 'hidden',
					'name' => 'position',
					'required' => false
				),
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
					'label' => $this->l('Class:'),
					'name' => 'class_name',
					'required' => true
				),
				array(
					'type' => 'text',
					'label' => $this->l('Module:'),
					'name' => 'module'
				),
				array(
					'type' => 'file',
					'label' => $this->l('Icon:'),
					'name' => 'icon',
					'desc' => $this->l('Upload a logo from your computer (.gif, .jpg, .jpeg or .png).')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status:'),
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
					),
					'desc' => $this->l('Show or hide menu.')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Parent:'),
					'name' => 'id_parent',
					'options' => array(
						'query' => $tabs,
						'id' => 'id_tab',
						'name' => 'name'
					)
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		return parent::renderForm();
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

		$this->_where = 'AND a.`id_parent` = 0';
		$this->_orderBy = 'position';

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
			// override attributes
			$this->display = 'list';
			$this->lang = false;

			$this->addRowAction('edit');
			$this->addRowAction('delete');

			$this->_select = 'b.*';
			$this->_join = 'LEFT JOIN `'._DB_PREFIX_.'tab_lang` b ON (b.`id_tab` = a.`id_tab` AND b.`id_lang` = '.$this->context->language->id.')';
			$this->_where = 'AND a.`id_parent` = '.(int)$id;
			$this->_orderBy = 'position';

			// get list and force no limit clause in the request
			$this->getList($this->context->language->id);

			// Render list
			$helper = new HelperList();
			$helper->actions = $this->actions;
			$helper->list_skip_actions = $this->list_skip_actions;
			$helper->no_link = true;
			$helper->shopLinkType = '';
			$helper->identifier = $this->identifier;
			$helper->imageType = $this->imageType;
			$helper->toolbar_scroll = false;
			$helper->show_toolbar = false;
			$helper->orderBy = 'position';
			$helper->orderWay = 'ASC';
			$helper->currentIndex = self::$currentIndex;
			$helper->token = $this->token;
			$helper->table = $this->table;
			$helper->position_identifier = $this->position_identifier;
			// Force render - no filter, form, js, sorting ...
			$helper->simple_header = true;
			$content = $helper->generateList($this->_list, $this->fields_list);

			echo Tools::jsonEncode(array('use_parent_structure' => false, 'data' => $content));
		}

		die;
	}

	public function postProcess()
	{
		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
			return;
		}
		/* PrestaShop demo mode*/

		if (($id_tab = (int)Tools::getValue('id_tab')) && ($direction = Tools::getValue('move')) && Validate::isLoadedObject($tab = new Tab($id_tab)))
		{
			if ($tab->move($direction))
				Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
		}
		elseif (Tools::getValue('position') && !Tools::isSubmit('submitAdd'.$this->table))
		{
			if ($this->tabAccess['edit'] !== '1')
				$this->errors[] = Tools::displayError('You do not have permission to edit this.');
			elseif (!Validate::isLoadedObject($object = new Tab((int)Tools::getValue($this->identifier))))
				$this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').
					' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			if (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position')))
				$this->errors[] = Tools::displayError('Failed to update the position.');
			else
				Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.Tools::getAdminTokenLite('AdminTabs'));
		}
		elseif (Tools::isSubmit('submitAddtab') && Tools::getValue('id_tab') === Tools::getValue('id_parent'))
			$this->errors[] = Tools::displayError('You can\'t put this menu inside itself. ');
		else
		{
			// Temporary add the position depend of the selection of the parent category
			if (!Tools::isSubmit('id_tab')) // @todo Review
				$_POST['position'] = Tab::getNbTabs(Tools::getValue('id_parent'));
		}
		
		if (!count($this->errors))
			parent::postProcess();
		
	}

	protected function afterImageUpload()
	{
		if (!($obj = $this->loadObject(true)))
			return;
		@rename(_PS_IMG_DIR_.'t/'.$obj->id.'.gif', _PS_IMG_DIR_.'t/'.$obj->class_name.'.gif');
	}

	public function ajaxProcessUpdatePositions()
	{
		$way = (int)(Tools::getValue('way'));
		$id_tab = (int)(Tools::getValue('id'));
		$positions = Tools::getValue('tab');

		// when changing positions in a tab sub-list, the first array value is empty and needs to be removed
		if (!$positions[0])
		{
			unset($positions[0]);
			// reset indexation from 0
			$positions = array_merge($positions);
		}

		foreach ($positions as $position => $value)
		{
			$pos = explode('_', $value);

			if (isset($pos[2]) && (int)$pos[2] === $id_tab)
			{
				if ($tab = new Tab((int)$pos[2]))
					if (isset($position) && $tab->updatePosition($way, $position))
						echo 'ok position '.(int)$position.' for tab '.(int)$pos[1].'\r\n';
					else
						echo '{"hasError" : true, "errors" : "Can not update tab '.(int)$id_tab.' to position '.(int)$position.' "}';
				else
					echo '{"hasError" : true, "errors" : "This tab ('.(int)$id_tab.') can t be loaded"}';

				break;
			}
		}
	}
}
