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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8971 $
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
		$this->deleted = true;
		$this->colorOnBackground = false;
		$this->context = Context::getContext();
		$this->imageType = 'gif';

		$this->fieldImageSettings = array(
			'name' => 'icon',
			'dir' => 'os'
		);

		$this->requiredDatabase = true;

		$this->fieldImageSettings = array(
			'name' => 'icon',
			'dir' => 'os'
		);

		$this->fieldsDisplay = array(
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
				'title' => $this->l('Send e-mail to customer'),
				'align' => 'center',
				'icon' => array(
					'1' => 'enabled.gif',
					'0' => 'disabled.gif'
				),
				'width' => 190,
				'type' => 'bool',
				'orderby' => false
			),
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
				'title' => $this->l('E-mail template'),
				'width' => 120
			)
		);

		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

		parent::__construct();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$list_orders_status = parent::renderList();

		// Added new list

		// reset actions and query vars
		$this->actions = array();
		unset($this->fieldsDisplay, $this->_select, $this->_join, $this->_group, $this->_filterHaving, $this->_filter, $this->identifier);

		$this->table = 'order_return_state';
		$this->_defaultOrderBy = $this->identifier = 'id_order_return_state';
		$this->deleted = false;
		$this->toolbar_btn = array();
		$this->bulk_actions = array();
		$this->_orderBy = null;

		$this->addRowAction('editstatus');

		// test if a filter is applied for this list
		if (Tools::isSubmit('submitFilter'.$this->table) || $this->context->cookie->{'submitFilter'.$this->table} !== false)
			$this->filter = true;

		// test if a filter reset request is required for this list
		if (isset($_POST['submitReset'.$this->table]))
			$this->action = 'reset_filters';
		else
			$this->action = '';

		$this->fieldsDisplay = array(
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

		// call postProcess() for take care about actions and filters
		$this->postProcess();

		$this->toolbar_title = $this->l('Return statuses');
		$list_orders_returns_status = parent::renderList();

		return $list_orders_status.$list_orders_returns_status;
	}

	/**
	 * Display editaddresses action link
	 * @param string $token the token to add to the link
	 * @param int $id the identifier to add to the link
	 * @return string
	 */
	public function displayEditaddressesLink($token = null, $id)
	{
		if (!array_key_exists('editaddresses', self::$cache_lang))
			self::$cache_lang['editaddresses'] = $this->l('Edit Adresses');

		$this->context->smarty->assign(array(
			'href' => self::$currentIndex.
				'&'.$this->identifier.'='.$id.
				'&editaddresses&token='.($token != null ? $token : $this->token),
			'action' => self::$cache_lang['editaddresses'],
		));

		return $this->createTemplate('list_action_edit_adresses.tpl')->fetch();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Order statuses'),
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
					'desc' => $this->l('Order status (e.g., \'Pending\')')
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
					'desc' => $this->l('Status will be highlighted in this color. HTML colors only (e.g.,').' "lightblue", "#CC6600")'
				),
				array(
					'type' => 'checkbox',
					'name' => 'logable',
					'values' => array(
						'query' => array(
							array(
								'id' => 'on',
								'name' => $this->l('Consider the associated order as validated'),
								'val' => '1'
							),
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
							array(
								'id' => 'on',
								'name' => $this->l('Allow customer to download and view PDF version of invoice'),
								'val' => '1'
							),
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
							array(
								'id' => 'on',
								'name' => $this->l('Hide this state in order for customer'),
								'val' => '1'
							),
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
							array(
								'id' => 'on',
								'name' => $this->l('Send e-mail to customer when order is changed to this status'),
								'val' => '1'
							),
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
							array(
								'id' => 'on',
								'name' => $this->l('Set order as shipped'),
								'val' => '1'
							),
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
							array(
								'id' => 'on',
								'name' => $this->l('Set order as paid'),
								'val' => '1'
							),
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
					'hint' => $this->l('Only letters, number and -_ are allowed'),
					'desc' => $this->l('E-mail template for both .html and .txt')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
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
			'paid_on' => $this->getFieldValue($obj, 'paid')
		);

		return parent::renderForm();
	}

	public function initFormStatus()
	{
		$id_order_return_state = Tools::getValue('id_order_return_state');

		// Create Object OrderReturnState
		$order_return_state = new OrderReturnState($id_order_return_state);

		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Order statuses'),
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
					'desc' => $this->l('Order return status name')
				)
			),
			'submit' => array(
				'title' => $this->l('   Save   '),
				'class' => 'button'
			)
		);

		$this->initToolbar();
		$this->getlanguages();
		$helper = new HelperForm();
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'order_return_state';
		$helper->identifier = 'id_order_return_state';
		$helper->id = $order_return_state->id;
		$helper->toolbar_fix = false;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->getFieldsValue($order_return_state);
		$helper->toolbar_btn = $this->toolbar_btn;
		$helper->title = $this->l('Edit Order Statuses');
		$this->content .= $helper->generateForm($this->fields_form);
	}

	/**
	 * AdminController::initToolbar() override
	 * @see AdminController::initToolbar()
	 *
	 */
	public function initToolbar()
	{
		switch ($this->display)
		{
			case 'editstatus':
				$this->toolbar_btn['save'] = array(
					'href' => '#',
					'desc' => $this->l('Save')
				);

				// Default cancel button - like old back link
				if (!isset($this->no_back) || $this->no_back == false)
				{
					$back = Tools::safeOutput(Tools::getValue('back', ''));
					if (empty($back))
						$back = self::$currentIndex.'&token='.$this->token;

					$this->toolbar_btn['cancel'] = array(
						'href' => $back,
						'desc' => $this->l('Cancel')
					);
				}
			break;

			default:
				parent::initToolbar();
		}
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

	/**
	 * AdminController::init() override
	 * @see AdminController::init()
	 */
	public function init()
	{
		if (Tools::isSubmit('editstatus'))
			$this->display = 'editstatus';

		parent::init();
	}

	public function initContent()
	{
		if ($this->display == 'editstatus' || $this->display == 'editstatus')
		{
			$this->content .= $this->initFormStatus();

			$this->context->smarty->assign(array(
				'content' => $this->content
			));
		}
		else
			return parent::initContent();
	}

	/**
	 * Display editaddresses action link
	 * @param string $token the token to add to the link
	 * @param int $id the identifier to add to the link
	 * @return string
	 */
	public function displayEditstatusLink($token = null, $id)
	{
		if (!array_key_exists('editstatus', self::$cache_lang))
			self::$cache_lang['editstatus'] = $this->l('Edit Status');

		$this->context->smarty->assign(array(
			'href' => self::$currentIndex.
				'&'.$this->identifier.'='.$id.
				'&editstatus&token='.($token != null ? $token : $this->token),
			'action' => self::$cache_lang['editstatus'],
		));

		return $this->context->smarty->fetch('helpers/list/list_action_edit.tpl');
	}

	public function postProcess()
	{
		if (Tools::isSubmit('submitAddorder_return_state'))
		{
			$id_order_return_state = (int)Tools::getValue('id_order_return_state');

			// Create Object OrderReturnState
			$order_return_state = new OrderReturnState($id_order_return_state);

			$order_return_state->name = array();
			$languages = Language::getLanguages(false);
				foreach ($languages as $language)
					$order_return_state->name[$language['id_lang']] = Tools::getValue('name_'.$language['id_lang']);

			// Update object
			if (!$order_return_state->save())
				$this->errors[] = Tools::displayError('An error has occured: Can\'t save the current order return state');
			else
				Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
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
			if (!$_POST['send_email'])
			{
				$languages = Language::getLanguages(false);
				foreach ($languages as $language)
					$_POST['template_'.$language['id_lang']] = '';
			}

			return parent::postProcess();
		}
		else if (isset($_GET['delete'.$this->table]))
		{
			$order_state = new OrderState($_GET['id_order_state'], $this->context->language->id);
			if (!$order_state->isRemovable())
				$this->errors[] = $this->l('For security reasons, you cannot delete default order statuses.');
			else
				return parent::postProcess();
		}
		else if (isset($_POST['submitDelorder_state']))
		{
			foreach ($_POST[$this->table.'Box'] as $selection)
			{
				$order_state = new OrderState($selection, $this->context->language->id);
				if (!$order_state->isRemovable())
				{
					$this->errors[] = $this->l('For security reasons, you cannot delete default order statuses.');
					break;
				}
			}
			if (empty($this->errors))
				return parent::postProcess();
		}
		else
			return parent::postProcess();
	}
}


