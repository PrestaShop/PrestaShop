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

class AdminStatusesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'order_state';
		$this->className = 'OrderState';
		$this->lang = true;
		$this->deleted = false;
		$this->colorOnBackground = false;
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		$this->context = Context::getContext();
		$this->multishop_context = Shop::CONTEXT_ALL;
		$this->imageType = 'gif';
		$this->fieldImageSettings = array(
			'name' => 'icon',
			'dir' => 'os'
		);
		parent::__construct();
	}

	public function init()
	{
		if (Tools::isSubmit('addorder_return_state'))
			$this->display = 'add';
		if (Tools::isSubmit('updateorder_return_state'))
			$this->display = 'edit';

		return parent::init();
	}
	
	/**
	 * init all variables to render the order status list
	 */
	protected function initOrderStatutsList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?')
				)
			);
		
		$this->fields_list = array(
			'id_order_state' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto',
				'color' => 'color'
			),
			'logo' => array(
				'title' => $this->l('Icon'),
				'align' => 'center',
				'image' => 'os',
				'width' => 25,
				'orderby' => false,
				'search' => false
			),
			'send_email' => array(
				'title' => $this->l('Send email to customer'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'width' => 190,
				'type' => 'bool',
				'orderby' => false
			),
			'delivery' => array(
				'title' => $this->l('Delivery'),
				'align' => 'center',
				'width' => 25,
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'orderby' => false
			)
			,
			'invoice' => array(
				'title' => $this->l('Invoice'),
				'align' => 'center',
				'width' => 25,
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'type' => 'bool',
				'orderby' => false
			),
			'template' => array(
				'title' => $this->l('Email template'),
				'width' => 120
			)
		);
	}
	
	/**
	 * init all variables to render the order return list
	 */
	protected function initOrdersReturnsList()
	{
		$this->table = 'order_return_state';
		$this->_defaultOrderBy = $this->identifier = 'id_order_return_state';
		$this->deleted = false;
		$this->_orderBy = null;

		$this->addRowAction('editstatus');
		$this->addRowActionSkipList('delete', array(1, 2, 3, 4, 5));

		$this->fields_list = array(
			'id_order_return_state' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'align' => 'left',
				'width' => 'auto',
				'color' => 'color'
			)
		);
	}
	
	protected function initOrderReturnsForm()
	{
		$id_order_return_state = (int)Tools::getValue('id_order_return_state');

		// Create Object OrderReturnState
		$order_return_state = new OrderReturnState($id_order_return_state);
		
		//init field form variable for order return form
		$this->fields_form = array();
		
		//$this->initToolbar();
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'order_return_state';
		$helper->identifier = 'id_order_return_state';
		$helper->id = $order_return_state->id;
		$helper->toolbar_scroll = false;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		
		if ($order_return_state->id)
			$helper->fields_value = array(
				'name' => $this->getFieldValue($order_return_state, 'name'),
				'color' => $this->getFieldValue($order_return_state, 'color'),
			);
		else
			$helper->fields_value = $this->getFieldsValue($order_return_state);
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('Edit Order Status');
		return $helper;
	}
	
	/**
	 * Function used to render the list to display for this controller
	 */
	public function renderList()
	{
		//init and render the first list
		$this->initOrderStatutsList();
		$lists = parent::renderList();
		
		//init and render the second list
		$this->_filter = false;
		$this->initOrdersReturnsList();

		// call postProcess() to take care of actions and filters
		$this->postProcess();
		$this->toolbar_title = $this->l('Return status');
		$this->checkFilterForOrdersReturnsList();

		parent::initToolbar();
		$lists .= parent::renderList();
		
		return $lists;
	}
	
	protected function checkFilterForOrdersReturnsList()
	{
		// test if a filter is applied for this list
		if (Tools::isSubmit('submitFilter'.$this->table) || $this->context->cookie->{'submitFilter'.$this->table} !== false)
			$this->filter = true;

		// test if a filter reset request is required for this list
		if (isset($_POST['submitReset'.$this->table]))
			$this->action = 'reset_filters';
		else
			$this->action = '';
	
	}
	
	public function renderForm()
	{
		if (Tools::isSubmit('updateorder_state') || Tools::isSubmit('addorder_state'))
			return $this->renderOrderStatusForm();
		else if (Tools::isSubmit('updateorder_return_state') || Tools::isSubmit('addorder_return_state'))
			return $this->renderOrderReturnsForm();
	}
	
	protected function renderOrderStatusForm()
	{
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Order status'),
				'image' => '../img/admin/time.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Status name:'),
					'name' => 'name',
					'lang' => true,
					'size' => 40,
					'required' => true,
					'hint' => $this->l('Invalid characters: numbers and').' !<>,;?=+()@#"�{}_$%:',
					'desc' => $this->l('Order status (e.g. \'Pending\')')
				),
				array(
					'type' => 'file',
					'label' => $this->l('Icon:'),
					'name' => 'icon',
					'desc' => $this->l('Upload an icon from your computer (File type: .gif, suggested size: 16x16)')
				),
				array(
					'type' => 'color',
					'label' => $this->l('Color:'),
					'name' => 'color',
					'size' => 30,
					'desc' => $this->l('Status will be highlighted in this color. (HTML colors only)').' "lightblue", "#CC6600")'
				),
				array(
					'type' => 'checkbox',
					'name' => 'logable',
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('Consider the associated order as validated.'), 'val' => '1'),
							),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'checkbox',
					'name' => 'invoice',
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('Allow a customer to download and view PDF versions of their invoice.'), 'val' => '1'),
							),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'checkbox',
					'name' => 'hidden',
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('Hide this state in all customer orders'), 'val' => '1'),
							),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'checkbox',
					'name' => 'send_email',
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('Send an email to the customer when his/her order status has changed.'), 'val' => '1'),
							),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'checkbox',
					'name' => 'shipped',
					'values' => array(
						'query' => array(
							array('id' => 'on',  'name' => $this->l('Set the order as shipped'), 'val' => '1'),
							),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'checkbox',
					'name' => 'paid',
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('Set the order as paid'), 'val' => '1'),
							),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'checkbox',
					'name' => 'delivery',
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('Show delivery PDF'), 'val' => '1'),
							),
						'id' => 'id',
						'name' => 'name'
					)
				),
				array(
					'type' => 'select_template',
					'label' => $this->l('Template:'),
					'name' => 'template',
					'lang' => true,
					'options' => array(
						'query' => $this->getTemplates($this->context->language->iso_code),
						'id' => 'id',
						'name' => 'name'
					),
					'hint' => $this->l('Only letters, number and hashtags are allowed.'),
					'desc' => $this->l('Email template for both .html and .txt')
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		if (!($obj = $this->loadObject(true)))
			return;

		$this->fields_value = array(
			'logable_on' => $this->getFieldValue($obj, 'logable'),
			'invoice_on' => $this->getFieldValue($obj, 'invoice'),
			'hidden_on' => $this->getFieldValue($obj, 'hidden'),
			'send_email_on' => $this->getFieldValue($obj, 'send_email'),
			'shipped_on' => $this->getFieldValue($obj, 'shipped'),
			'paid_on' => $this->getFieldValue($obj, 'paid'),
			'delivery_on' => $this->getFieldValue($obj, 'delivery')
		);

		return parent::renderForm();
	}
	
	protected function renderOrderReturnsForm()
	{
		$helper = $this->initOrderReturnsForm();
			
		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Order status'),
				'image' => '../img/admin/time.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Status name:'),
					'name' => 'name',
					'lang' => true,
					'size' => 40,
					'required' => true,
					'hint' => $this->l('Invalid characters: numbers and').' !<>,;?=+()@#"�{}_$%:',
					'desc' => $this->l('Order\'s return status name')
				),
				array(
					'type' => 'color',
					'label' => $this->l('Color:'),
					'name' => 'color',
					'size' => 30,
					'desc' => $this->l('Status will be highlighted in this color. (HTML colors only)').' "lightblue", "#CC6600")'
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);
		return $helper->generateForm($this->fields_form);
	}
	
	protected function getTemplates($iso_code)
	{
		$array = array();
		if (!file_exists(_PS_ADMIN_DIR_.'/../mails/'.$iso_code))
			return false;
		$templates = scandir(_PS_ADMIN_DIR_.'/../mails/'.$iso_code);
		foreach ($templates as $key => $template)
			if (!strncmp(strrev($template), 'lmth.', 5))
				$array[] = array(
							'id' => substr($template, 0, -5),
							'name' => substr($template, 0, -5)
				);

		return $array;
	}

	public function postProcess()
	{
		if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway'))
			$this->filter = true;
				
		if (Tools::isSubmit('submitAddorder_return_state') || Tools::isSubmit('submitAddorder_return_state'))
		{
			$id_order_return_state = Tools::getValue('id_order_return_state');

			// Create Object OrderReturnState
			$order_return_state = new OrderReturnState((int)$id_order_return_state);

			$order_return_state->color = Tools::getValue('color');
			$order_return_state->name = array();
			$languages = Language::getLanguages(false);
				foreach ($languages as $language)
					$order_return_state->name[$language['id_lang']] = Tools::getValue('name_'.$language['id_lang']);

			// Update object
			if (!$order_return_state->save())
				$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current order\'s return state.');
			else
				Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
		}
		
		if (Tools::isSubmit('submitBulkdeleteorder_return_state'))
		{
			$this->className = 'OrderReturnState';
			$this->table = 'order_return_state';
			$this->boxes = Tools::getValue('order_return_stateBox');
			parent::processBulkDelete();
		}

		if (Tools::isSubmit('deleteorder_return_state'))
		{
			$id_order_return_state = Tools::getValue('id_order_return_state');

			// Create Object OrderReturnState
			$order_return_state = new OrderReturnState((int)$id_order_return_state);
			
			if (!$order_return_state->delete())
				$this->errors[] = Tools::displayError('An error has occurred: Can\'t delete the current order\'s return state.');
			else
				Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.$this->token);
		}
		
		if (Tools::isSubmit('submitAdd'.$this->table))
		{
			$this->deleted = false; // Disabling saving historisation
			$_POST['invoice'] = (int)Tools::getValue('invoice_on');
			$_POST['logable'] = (int)Tools::getValue('logable_on');
			$_POST['send_email'] = (int)Tools::getValue('send_email_on');
			$_POST['hidden'] = (int)Tools::getValue('hidden_on');
			$_POST['shipped'] = (int)Tools::getValue('shipped_on');
			$_POST['paid'] = (int)Tools::getValue('paid_on');
			$_POST['delivery'] = (int)Tools::getValue('delivery_on');
			if (!$_POST['send_email'])
			{
				$languages = Language::getLanguages(false);
				foreach ($languages as $language)
					$_POST['template_'.$language['id_lang']] = '';
			}

			return parent::postProcess();
		}
		else if (Tools::isSubmit('delete'.$this->table))
		{
			$order_state = new OrderState(Tools::getValue('id_order_state'), $this->context->language->id);
			if (!$order_state->isRemovable())
				$this->errors[] = $this->l('For security reasons, you cannot delete default order statuses.');
			else
				return parent::postProcess();
		}
		else if (Tools::isSubmit('submitBulkdelete'.$this->table))
		{
			foreach (Tools::getValue($this->table.'Box') as $selection)
			{
				$order_state = new OrderState((int)$selection, $this->context->language->id);
				if (!$order_state->isRemovable())
				{
					$this->errors[] = $this->l('For security reasons, you cannot delete default order statuses.');
					break;
				}
			}

			if (!count($this->errors))
				return parent::postProcess();
		}
		else
			return parent::postProcess();
	}
	
	protected function filterToField($key, $filter)
	{
		if ($this->table == 'order_state')
			$this->initOrderStatutsList();
		elseif ($this->table == 'order_return_state')
			$this->initOrdersReturnsList();
		return parent::filterToField($key, $filter);
	}
}
