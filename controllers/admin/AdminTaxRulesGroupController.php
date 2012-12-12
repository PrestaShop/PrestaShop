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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminTaxRulesGroupControllerCore extends AdminController
{
	public $tax_rule;
	public $selected_countries = array();
	public $selected_states = array();
	public $errors_tax_rule;

	public function __construct()
	{
		$this->table = 'tax_rules_group';
		$this->className = 'TaxRulesGroup';
		$this->lang = false;

		$this->context = Context::getContext();

		$this->fields_list = array(
			'id_tax_rules_group' => array(
				'title' => $this->l('ID'),
				'width' => 25
			),
			'name' => array(
				'title' => $this->l('Name'),
				'width' => 'auto'
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'width' => 25,
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'align' => 'center'
			)
		);

		$this->bulk_actions = array(
			'delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')),
			'enableSelection' => array('text' => $this->l('Enable selection')),
			'disableSelection' => array('text' => $this->l('Disable selection'))
		);

		parent::__construct();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		return parent::renderList();
	}

	public function initRulesList($id_group)
	{
		$this->table = 'tax_rule';
		$this->identifier = 'id_tax_rule';
		$this->className = 'TaxRule';
		$this->lang = false;
		$this->list_simple_header = false;
		$this->toolbar_btn = null;
		$this->list_no_link = true;

		$this->fields_list = array(
			'country_name' => array(
				'title' => $this->l('Country'),
				'width' => 140
			),
			'state_name' => array(
				'title' => $this->l('State'),
				'width' => 140
			),
			'zipcode' => array(
				'title' => $this->l('Zip Codes'),
				'width' => 25,
			),
			'behavior' => array(
				'title' => $this->l('Behavior'),
				'width' => 25,
			),
			'rate' => array(
				'title' => $this->l('Tax'),
				'width' => 25,
			),
			'description' => array(
				'title' => $this->l('Description'),
				'width' => 25,
			)
		);

		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->_select = '
			c.`name` AS country_name,
			s.`name` AS state_name,
			CONCAT_WS(" - ", a.`zipcode_from`, a.`zipcode_to`) AS zipcode,
			t.rate';

		$this->_join = '
			LEFT JOIN `'._DB_PREFIX_.'country_lang` c
				ON (a.`id_country` = c.`id_country` AND id_lang = '.(int)$this->context->language->id.')
			LEFT JOIN `'._DB_PREFIX_.'state` s
				ON (a.`id_state` = s.`id_state`)
			LEFT JOIN `'._DB_PREFIX_.'tax` t
				ON (a.`id_tax` = t.`id_tax`)';
		$this->_where = 'AND `id_tax_rules_group` = '.(int)$id_group;

		$this->show_toolbar = false;

		return parent::renderList();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Tax Rules'),
				'image' => '../img/admin/dollar.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Name:'),
					'name' => 'name',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Enable:'),
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
				)
			),
			'submit' => array(
				'title' => $this->l('Save and stay'),
				'class' => 'button',
				'stay' => true
			)
		);

		if (!($obj = $this->loadObject(true)))
			return;
		if (!isset($obj->id))
		{
			$this->no_back = false;
			$content = parent::renderForm();
		}
		else
		{
			$this->no_back = true;
			$this->toolbar_btn['new'] = array(
				'href' => '#',
				'desc' => $this->l('Add new tax rule')
			);
			$content = parent::renderForm();
			$this->tpl_folder = 'tax_rules/';
			$content .= $this->initRuleForm();

			// We change the variable $ tpl_folder to avoid the overhead calling the file in list_action_edit.tpl in intList ();

			$content .= $this->initRulesList((int)$obj->id);
		}
		return $content;
	}

	public function initRuleForm()
	{
		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('New tax rule'),
				'image' => '../img/admin/dollar.gif'
			),
			'input' => array(
				array(
					'type' => 'select',
					'label' => $this->l('Country:'),
					'name' => 'country',
					'id' => 'country',
					'options' => array(
						'query' => Country::getCountries($this->context->language->id),
						'id' => 'id_country',
						'name' => 'name',
						'default' => array(
							'value' => 0,
							'label' => $this->l('All')
						)
					)
				),
				array(
					'type' => 'select',
					'label' => $this->l('State:'),
					'name' => 'states[]',
					'id' => 'states',
					'multiple' => true,
					'size' => 5,
					'options' => array(
						'query' => array(),
						'id' => 'id_state',
						'name' => 'name',
						'default' => array(
							'value' => 0,
							'label' => $this->l('All')
						)
					)
				),
				array(
					'type' => 'hidden',
					'name' => 'action'
				),
				array(
					'type' => 'hidden',
					'name' => 'id_tax_rules_group'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Zip Code range:'),
					'name' => 'zipcode',
					'required' => false,
					'hint' => $this->l('You can define a range (eg: 75000-75015) or a simple zipcode')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Behavior:'),
					'name' => 'behavior',
					'required' => false,
					'options' => array(
						'query' => array(
							array(
								'id' => 0,
								'name' => $this->l('This tax only')
							),
							array(
								'id' => 1,
								'name' => $this->l('Combine')
							),
							array(
								'id' => 2,
								'name' => $this->l('One After Another')
							)
						),
						'id' => 'id',
						'name' => 'name'
					),
					'hint' =>
						$this->l('Define the behavior if an address matches multiple rules:').'<br />
						<b>'.$this->l('This Tax Only:').'</b> '.$this->l('Will apply only this tax').'<br />
						<b>'.$this->l('Combine:').'</b> '.$this->l('Combine taxes (eg: 10% + 5% => 15%)').'<br />
						<b>'.$this->l('One After Another:').'</b> '.$this->l('Apply taxes one after another (eg: 100€ + 10% => 110€ + 5% => 115.5€)')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Tax:'),
					'name' => 'id_tax',
					'required' => false,
					'options' => array(
						'query' => Tax::getTaxes((int)$this->context->language->id),
						'id' => 'id_tax',
						'name' => 'name',
						'default' => array(
							'value' => 'name',
							'label' => $this->l('No Tax')
						)
					),
					'desc' => sprintf($this->l('(Total tax: %s)'), '9%')
				),
				array(
					'type' => 'text',
					'label' => $this->l('Description:'),
					'name' => 'description',
					'size' => 40,
				)
			),
			'submit' => array(
				'title' => $this->l('Save and stay'),
				'class' => 'button',
				'stay' => true
			)
		);

		if (!($obj = $this->loadObject(true)))
			return;

		$this->fields_value = array(
			'action' => 'create_rule',
			'id_tax_rules_group' => $obj->id,
			'id_tax_rule' => ''
		);

		$this->getlanguages();
		$helper = new HelperForm();
		$helper->override_folder = $this->tpl_folder;
		$helper->currentIndex = self::$currentIndex;
		$helper->token = $this->token;
		$helper->table = 'tax_rule';
		$helper->identifier = 'id_tax_rule';
		$helper->id = $obj->id;
		$helper->toolbar_scroll = true;
		$helper->show_toolbar = false;
		$helper->languages = $this->_languages;
		$helper->default_form_language = $this->default_form_language;
		$helper->allow_employee_form_lang = $this->allow_employee_form_lang;
		$helper->fields_value = $this->getFieldsValue($this->object);
		$helper->toolbar_btn = null;
		$helper->submit_action = 'create_rule';

		return $helper->generateForm($this->fields_form);
	}


	public function initProcess()
	{
		if (Tools::isSubmit('deletetax_rule'))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'delete_tax_rule';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else if (Tools::isSubmit('submitBulkdeletetax_rule'))
		{
			if ($this->tabAccess['delete'] === '1')
				$this->action = 'bulk_delete_tax_rules';
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete here.');
		}
		else if (Tools::getValue('action') == 'create_rule')
		{
			if ($this->tabAccess['add'] === '1')
				$this->action = 'create_rule';
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		else
			parent::initProcess();

	}

	protected function processCreateRule()
	{
		$zip_code = Tools::getValue('zipcode');
		$id_rule = (int)Tools::getValue('id_tax_rule');
		$id_tax = (int)Tools::getValue('id_tax');
		$id_tax_rules_group = (int)Tools::getValue('id_tax_rules_group');
		$behavior = (int)Tools::getValue('behavior');
		$description = pSQL(Tools::getValue('description'));

		if ((int)($id_country = Tools::getValue('country')) == 0)
		{
			$countries = Country::getCountries($this->context->language->id);
			$this->selected_countries = array();
			foreach ($countries as $country)
				$this->selected_countries[] = (int)$country['id_country'];
		}
		else
			$this->selected_countries = array($id_country);
		$this->selected_states = Tools::getValue('states');

		if (empty($this->selected_states) || count($this->selected_states) == 0)
			$this->selected_states = array(0);

		foreach ($this->selected_countries as $id_country)
		{
			foreach ($this->selected_states as $id_state)
			{
				$tr = new TaxRule();

				// update or creation?
				if (isset($id_rule))
					$tr->id = $id_rule;

				$tr->id_tax = $id_tax;
				$tr->id_tax_rules_group = (int)$id_tax_rules_group;
				$tr->id_country = (int)$id_country;
				$tr->id_state = (int)$id_state;
				list($tr->zipcode_from, $tr->zipcode_to) = $tr->breakDownZipCode($zip_code);

				// Construct Object Country
				$country = new Country((int)$id_country);

				if ($zip_code && $country->need_zip_code)
				{
					if ($country->zip_code_format)
					{
						foreach (array($tr->zipcode_from, $tr->zipcode_to) as $zip_code)
							if ($zip_code)
								if (!$country->checkZipCode($zip_code))
								{
									$this->errors[] = sprintf(
										Tools::displayError('Zip/Postal code is invalid. Must be typed as follows: %s'),
										str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format)))
									);
								}
					}
				}

				$tr->behavior = (int)$behavior;
				$tr->description = $description;
				$this->tax_rule = $tr;
				$_POST['id_state'] = $tr->id_state;

				$this->errors = array_merge($this->errors, $this->validateTaxRule($tr));

				if (count($this->errors) == 0)
					if (!$tr->save())
						$this->errors[] = Tools::displayError('An error has occurred: Can\'t save the current tax rule');
			}
		}

		if (count($this->errors) == 0)
			Tools::redirectAdmin(
				self::$currentIndex.'&'.$this->identifier.'='.(int)$id_tax_rules_group.'&conf=4&update'.$this->table.'&token='.$this->token
			);
		else
			$this->display = 'edit';
	}

	protected function processBulkDeleteTaxRules()
	{
		$this->deleteTaxRule(Tools::getValue('tax_ruleBox'));
	}

	protected function processDeleteTaxRule()
	{
		$this->deleteTaxRule(array(Tools::getValue('id_tax_rule')));
	}

	protected function deleteTaxRule(array $id_tax_rule_list)
	{
		$result = true;

		foreach ($id_tax_rule_list as $id_tax_rule)
		{
			$tax_rule = new TaxRule((int)$id_tax_rule);
			if (Validate::isLoadedObject($tax_rule))
				$result &= $tax_rule->delete();
		}

		Tools::redirectAdmin(
			self::$currentIndex.'&'.$this->identifier.'='.(int)$tax_rule->id_tax_rules_group.'&conf=4&update'.$this->table.'&token='.$this->token
		);
	}


	/**
	* check if the tax rule could be added in the database
	* @param TaxRule $tr
	*/
	protected function validateTaxRule(TaxRule $tr)
	{
		// TODO: check if the rule already exists
		return $tr->validateController();
	}
}

