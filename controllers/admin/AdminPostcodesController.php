<?php
/*
* 2007-2015 PrestaShop
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
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminPostcodesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'postcode';
		$this->className = 'Postcode';
		$this->lang = false;
		$this->requiredDatabase = true;

		$this->context = Context::getContext();

		if (!Tools::getValue('realedit'))
			$this->deleted = false;

		$this->_select = 'z.`name` AS zone, cl.`name` AS country';
		$this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = a.`id_zone`)
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (cl.`id_country` = a.`id_country` AND cl.id_lang = '.(int)$this->context->language->id.')';
		
		$countries_array = $zones_array = array();
		$this->zones = Zone::getZones();
		$this->countries = Country::getCountries($this->context->language->id, false, true, false);
		foreach ($this->zones as $zone)
			$zones_array[$zone['id_zone']] = $zone['name'];
		foreach ($this->countries as $country)
			$countries_array[$country['id_country']] = $country['name'];

		$this->fields_list = array(
			'id_postcode' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'postcode' => array(
				'title' => $this->l('Postcode'),
				'filter_key' => 'a!postcode'
			),
			'zone' => array(
				'title' => $this->l('Zone'),
				'type' => 'select',
				'list' => $zones_array,
				'filter_key' => 'z!id_zone',
				'filter_type' => 'int',
				'order_key' => 'zone'
			),
			'country' => array(
				'title' => $this->l('Country'),
				'type' => 'select',
				'list' => $countries_array,
				'filter_key' => 'cl!id_country',
				'filter_type' => 'int',
				'order_key' => 'country'
			),
			'active' => array(
				'title' => $this->l('Enabled'),
				'align' => 'center',
				'active' => 'status',
				'type' => 'bool',
				'orderby' => false,
				'class' => 'fixed-width-sm'
			)
		);

		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);

		parent::__construct();
	}

	public function initPageHeaderToolbar()
	{
		if (empty($this->display))
			$this->page_header_toolbar_btn['new_postcode'] = array(
				'href' => self::$currentIndex.'&addpostcode&token='.$this->token,
				'desc' => $this->l('Add new postcode', null, null, false),
				'icon' => 'process-icon-new'
			);

		parent::initPageHeaderToolbar();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		return parent::renderList();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Postcodes'),
				'icon' => 'icon-globe'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Postcode'),
					'name' => 'postcode',
					'maxlength' => 32,
					'required' => true,
					'hint' => $this->l('Provide the postcode to add to a zone.')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Country'),
					'name' => 'id_country',
					'required' => true,
					'default_value' => (int)$this->context->country->id,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id, false, true),
						'id' => 'id_country',
						'name' => 'name',
					),
					'hint' => $this->l('Country where the postcode is located.')
				),
				array(
					'type' => 'select',
					'label' => $this->l('Zone'),
					'name' => 'id_zone',
					'required' => true,
					'options' => array(
						'query' => Zone::getZones(),
						'id' => 'id_zone',
						'name' => 'name'
					),
					'hint' => array(
						$this->l('Geographical region where this postcode is located.'),
						$this->l('Used for shipping')
					)
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Status'),
					'name' => 'active',
					'required' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => '<img src="../img/admin/enabled.gif" alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" />'
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => '<img src="../img/admin/disabled.gif" alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" />'
						)
					)
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);

		return parent::renderForm();
	}

	public function postProcess()
	{
		if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway'))
			$this->filter = true;

		/* Delete postcode */
		if (Tools::isSubmit('delete'.$this->table))
		{
			if ($this->tabAccess['delete'] === '1')
			{
				if (Validate::isLoadedObject($object = $this->loadObject()))
				{
					if ($object->delete())
						Tools::redirectAdmin(self::$currentIndex.'&conf=1&token='.(Tools::getValue('token') ? Tools::getValue('token') : $this->token));
					$this->errors[] = Tools::displayError('An error occurred during deletion.');
				}
				else
					$this->errors[] = Tools::displayError('An error occurred while deleting the object.').' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to delete this.');
		}

		if (!count($this->errors))
			parent::postProcess();
	}

	protected function displayAjaxPostcodes()
	{
		if ($this->tabAccess['view'] === '1')
		{
			$postcodes = Db::getInstance()->executeS('
			SELECT p.id_postcode, p.postcode
			FROM '._DB_PREFIX_.'postcode p
			LEFT JOIN '._DB_PREFIX_.'country c ON (p.`id_country` = c.`id_country`)
			WHERE p.id_country = '.(int)(Tools::getValue('id_country')).' AND p.active = 1
			ORDER BY p.`postcode` ASC');

			if (is_array($postcodes) AND !empty($postcodes))
			{
				$list = '';
				if ((bool)Tools::getValue('no_empty') != true)
				{
					$empty_value = (Tools::isSubmit('empty_value')) ? Tools::getValue('empty_value') : '-';
					$list = '<option value="0">'.Tools::htmlentitiesUTF8($empty_value).'</option>'."\n";
				}

				foreach ($postcodes AS $postcode)
					$list .= '<option value="'.(int)($postcode['id_postcode']).'"'.((isset($_GET['id_postcode']) AND $_GET['id_postcode'] == $state['id_postcode']) ? ' selected="selected"' : '').'>'.$postcode['postcode'].'</option>'."\n";
			}
			else
				$list = 'false';

			die($list);
		}
	}

}
