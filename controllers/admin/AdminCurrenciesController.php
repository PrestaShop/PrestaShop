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

class AdminCurrenciesControllerCore extends AdminController
{
	public function __construct()
	{
		$this->table = 'currency';
		$this->className = 'Currency';
		$this->lang = false;

		$this->fields_list = array(
			'id_currency' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'name' => array('title' => $this->l('Currency')),
			'iso_code' => array('title' => $this->l('ISO code'), 'align' => 'center', 'width' => 80),
			'iso_code_num' => array('title' => $this->l('ISO code number'), 'align' => 'center', 'width' => 120),
			'sign' => array('title' => $this->l('Symbol'), 'width' => 20, 'align' => 'center', 'orderby' => false, 'search' => false),
			'conversion_rate' => array('title' => $this->l('Conversion rate'), 'type' => 'float', 'align' => 'center', 'width' => 130, 'search' => false),
			'active' => array('title' => $this->l('Enabled'), 'width' => 25, 'align' => 'center', 'active' => 'status', 'type' => 'bool', 'orderby' => false),
		);

	 	$this->bulk_actions = array(
			'delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')),
			'enableSelection' => array('text' => $this->l('Enable selection')),
			'disableSelection' => array('text' => $this->l('Disable selection'))
			);

		$this->fields_options = array(
			'change' => array(
				'title' =>	$this->l('Currency rates'),
				'image' => '../img/admin/exchangesrate.gif',
				'description' => $this->l('Use PrestaShop\'s webservice to update your currency exchange rates. Please use caution, rates are provided as-is.'),
				'submit' => array(
					'title' => $this->l('Update currency rates'),
					'class' => 'button',
					'name' => 'SubmitExchangesRates'
				)
			),
			'cron' => array(
				'title' =>	$this->l('Automatically update currency rates'),
				'image' => '../img/admin/tab-tools.gif',
				'info' => $this->l('Use PrestaShop\'s webservice to update your currency exchange rates. Please use caution, rates are provided as-is. Place this URL in crontab or access it manually daily').':<br />
					<b>'.Tools::getShopDomain(true, true).__PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/cron_currency_rates.php?secure_key='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')).'</b></p>',
			)
		);

		parent::__construct();

		$this->_select .= 'currency_shop.conversion_rate conversion_rate';
		$this->_join .= Shop::addSqlAssociation('currency', 'a');
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		$this->_where = 'AND a.`deleted` = 0';

		return parent::renderList();
	}

	public function renderForm()
	{
		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Currencies:'),
				'image' => '../img/admin/money.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Currency name:'),
					'name' => 'name',
					'size' => 30,
					'maxlength' => 32,
					'required' => true,
					'hint' => $this->l('Only letters and the minus character are allowed')
				),
				array(
					'type' => 'text',
					'label' => $this->l('ISO code:'),
					'name' => 'iso_code',
					'size' => 30,
					'maxlength' => 32,
					'required' => true,
					'desc' => $this->l('ISO code (e.g. USD for Dollars, EUR for Euros)').'...',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Numeric ISO code:'),
					'name' => 'iso_code_num',
					'size' => 30,
					'maxlength' => 32,
					'required' => true,
					'desc' => $this->l('Numeric ISO code (e.g. 840 for Dollars, 978 for Euros)').'...',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Symbol:'),
					'name' => 'sign',
					'size' => 3,
					'maxlength' => 8,
					'required' => true,
					'desc' => $this->l('Will appear in Front Office (e.g. $, €)').'...',
				),
				array(
					'type' => 'text',
					'label' => $this->l('Conversion rate:'),
					'name' => 'conversion_rate',
					'size' => 3,
					'maxlength' => 11,
					'required' => true,
					'desc' => $this->l('Conversion rate from one unit of your shop\'s default currency (for example, 1€) to this currency. For example, if the default currency is euros and this currency is dollars, type \'1.20\'').' 1&euro; = $1.20',
				),
				array(
					'type' => 'select',
					'label' => $this->l('Formatting:'),
					'name' => 'format',
					'size' => 3,
					'maxlength' => 11,
					'required' => true,
					'desc' =>$this->l('Applies to all prices, e.g.').' $1,240.15',
					'options' => array(
						'query' => array(
							array('key' => 1, 'name' => 'X0,000.00 ('.$this->l('as with Dollars').')'),
							array('key' => 2, 'name' => '0 000,00X ('.$this->l('as with Euros').')'),
							array('key' => 3, 'name' => 'X0.000,00'),
							array('key' => 4, 'name' => '0,000.00X'),
							array('key' => 5, 'name' => '0 000.00X') // Added for the switzerland currency
						),
						'name' => 'name',
						'id' => 'key'
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Decimals:'),
					'name' => 'decimals',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('Display decimals in prices'),
					'values' => array(
						array(
							'id' => 'decimals_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'decimals_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Spacing:'),
					'name' => 'blank',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'desc' => $this->l('Include a space between symbol and price, e.g.').'<br />$1,240.15 -> $ 1,240.15',
					'values' => array(
						array(
							'id' => 'blank_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'blank_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
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
			'title' => $this->l('   Save   '),
			'class' => 'button'
		);

		return parent::renderForm();
	}

	/**
	 * @see AdminController::processDelete()
	 */
	public function processDelete()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			if ($object->id == Configuration::get('PS_CURRENCY_DEFAULT'))
				$this->errors[] = $this->l('You cannot delete the default currency');
			else if ($object->delete())
				Tools::redirectAdmin(self::$currentIndex.'&conf=1'.'&token='.$this->token);
			else
				$this->errors[] = Tools::displayError('An error occurred during deletion.');
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while deleting object.').'
				<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
	}

	/**
	 * @see AdminController::processStatus()
	 */
	public function processStatus()
	{
		if (Validate::isLoadedObject($object = $this->loadObject()))
		{
			if ($object->active && $object->id == Configuration::get('PS_CURRENCY_DEFAULT'))
				$this->errors[] = $this->l('You cannot disable the default currency');
			else if ($object->toggleStatus())
				Tools::redirectAdmin(self::$currentIndex.'&conf=5'.((($id_category =
					(int)Tools::getValue('id_category')) && Tools::getValue('id_product')) ? '&id_category='.$id_category : '').'&token='.$this->token);
			else
				$this->errors[] = Tools::displayError('An error occurred while updating status.');
		}
		else
			$this->errors[] = Tools::displayError('An error occurred while updating status for object.').'
				<b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
	}

	/**
	 * Update currency exchange rates
	 */
	public function processExchangeRates()
	{
		if (!$this->errors = Currency::refreshCurrencies())
			Tools::redirectAdmin(self::$currentIndex.'&conf=6&token='.$this->token);
	}

	/**
	 * @see AdminController::initProcess()
	 */
	public function initProcess()
	{
		if (Tools::isSubmit('SubmitExchangesRates'))
		{
			if ($this->tabAccess['edit'] === '1')
				$this->action = 'exchangeRates';
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		if (Tools::isSubmit('submitAddcurrency') && !Tools::getValue('id_currency') && Currency::exists(Tools::getValue('iso_code'), Tools::getValue('iso_code_num')))
				$this->errors[] = Tools::displayError('This currency already exist.');
		parent::initProcess();
	}
}

