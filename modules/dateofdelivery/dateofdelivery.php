<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;
	
class DateOfDelivery extends Module
{
	private $_html = '';

	public function __construct()
	{
		$this->name = 'dateofdelivery';
		$this->tab = 'shipping_logistics';
		$this->version = '1.2';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;
		
		$this->bootstrap = true;
		parent::__construct();	
		
		$this->displayName = $this->l('Date of delivery');
		$this->description = $this->l('Displays an approximate date of delivery');
	}
	
	public function install()
	{	
		if (!parent::install()
			|| !$this->registerHook('beforeCarrier')
			|| !$this->registerHook('orderDetailDisplayed')
			||!$this->registerHook('displayPDFInvoice'))
				return false;
		
		if (!Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'dateofdelivery_carrier_rule` (
			`id_carrier_rule` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`id_carrier` INT NOT NULL,
			`minimal_time` INT NOT NULL,
			`maximal_time` INT NOT NULL,
			`delivery_saturday` TINYINT(1) NOT NULL,
			`delivery_sunday` TINYINT(1) NOT NULL
		) ENGINE ='._MYSQL_ENGINE_.';
		'))
			return false;
		
		Configuration::updateValue('DOD_EXTRA_TIME_PRODUCT_OOS', 0);
		Configuration::updateValue('DOD_EXTRA_TIME_PREPARATION', 1);
		Configuration::updateValue('DOD_PREPARATION_SATURDAY', 1);
		Configuration::updateValue('DOD_PREPARATION_SUNDAY', 1);
		Configuration::updateValue('DOD_DATE_FORMAT', 'l j F Y');
		
		return true;
	}
	
	public function uninstall()
	{
		Configuration::deleteByName('DOD_EXTRA_TIME_PRODUCT_OOS');
		Configuration::deleteByName('DOD_EXTRA_TIME_PREPARATION');
		Configuration::deleteByName('DOD_PREPARATION_SATURDAY');
		Configuration::deleteByName('DOD_PREPARATION_SUNDAY');
		Configuration::deleteByName('DOD_DATE_FORMAT');
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'dateofdelivery_carrier_rule`');
		
		return parent::uninstall();
	}

	public function getContent()
	{		
		$this->_html .= '';
		
		$this->_postProcess();
		if (Tools::isSubmit('addCarrierRule') OR (Tools::isSubmit('updatedateofdelivery') AND Tools::isSubmit('id_carrier_rule')))
			$this->_html .= $this->renderAddForm();
		else
		{
			$this->_html .= $this->renderList();
			$this->_html .= $this->renderForm();
		}
			
		
		return $this->_html;
	}

	public function hookBeforeCarrier($params)
	{
		if (!isset($params['delivery_option_list']) || !count($params['delivery_option_list']))
			return false;
		
		$package_list = $params['cart']->getPackageList();

		$datesDelivery = array();
		foreach ($params['delivery_option_list'] as $id_address => $by_address)
		{
			$datesDelivery[$id_address] = array();
			foreach ($by_address as $key => $delivery_option)
			{
				$date_from = null;
				$date_to = null;
				$datesDelivery[$id_address][$key] = array();
				
				foreach ($delivery_option['carrier_list'] as $id_carrier => $carrier)
				{
					foreach ($carrier['package_list'] as $id_package)
					{
						$package = $package_list[$id_address][$id_package];
						$oos = false; // For out of stock management
						foreach ($package['product_list'] as $product)
							if (StockAvailable::getQuantityAvailableByProduct($product['id_product'], ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), (int)$this->context->shop->id) <= 0)
							{
								$oos = true;
								break;
							}
						$available_date = Product::getAvailableDate($product['id_product'], ($product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null), (int)$this->context->shop->id);
						$date_range = $this->_getDatesOfDelivery($id_carrier, $oos, $available_date);
						if (is_null($date_from) || $date_from < $date_range[0])
						{
							$date_from = $date_range[0][1];
							$datesDelivery[$id_address][$key][0] = $date_range[0];
						}
						if (is_null($date_to) || $date_to < $date_range[1])
						{
							$date_to = $date_range[1][1];
							$datesDelivery[$id_address][$key][1] = $date_range[1];
						}
					}
				}
			}
		}
		$this->smarty->assign(array(
			'nbPackages' => $params['cart']->getNbOfPackages(),
			'datesDelivery' => $datesDelivery,
			'delivery_option' => $params['delivery_option']
		));
		
		return $this->display(__FILE__, 'beforeCarrier.tpl');
	}
	
	public function hookOrderDetailDisplayed($params)
	{
		
		$oos = false; // For out of stock management
		foreach ($params['order']->getProducts() as $product)
			if ($product['product_quantity_in_stock'] < 1)
				$oos = true;
		
		$datesDelivery = array();		
		$datesDelivery = $this->_getDatesOfDelivery((int)($params['order']->id_carrier), $oos, $params['order']->date_add);
		
		if (!is_array($datesDelivery) OR !sizeof($datesDelivery))
			return ;
			
		$this->smarty->assign('datesDelivery', $datesDelivery);
		
		return $this->display(__FILE__, 'orderDetail.tpl');
	}

	/**
	 * Displays the delivery dates on the invoice
	 *
	 * @param $params contains an instance of OrderInvoice
	 * @return string
	 *
	 */
	public function hookDisplayPDFInvoice($params)
	{
		$order_invoice = $params['object'];
		if (!($order_invoice instanceof OrderInvoice))
			return;

		$order = new Order((int)$order_invoice->id_order);

		$oos = false; // For out of stock management
		foreach ($order->getProducts() as $product)
			if ($product['product_quantity_in_stock'] < 1)
				$oos = true;

		$id_carrier = (int)OrderInvoice::getCarrierId($order_invoice->id);
		$return = '';
		if (($datesDelivery = $this->_getDatesOfDelivery($id_carrier, $oos, $order_invoice->date_add)) && isset($datesDelivery[0][0]) && isset($datesDelivery[1][0]))
			$return = sprintf($this->l('Approximate date of delivery is between %1$s and %2$s'), $datesDelivery[0][0], $datesDelivery[1][0]);

		return $return;
	}

	private function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitMoreOptions'))
		{
			if (Tools::getValue('date_format') == '' OR !Validate::isCleanHtml(Tools::getValue('date_format')))
				$errors[] = $this->l('Date format is invalid');
			
			if (!sizeof($errors))
			{
				Configuration::updateValue('DOD_EXTRA_TIME_PRODUCT_OOS', (int)Tools::getValue('extra_time_product_oos'));
				Configuration::updateValue('DOD_EXTRA_TIME_PREPARATION', (int)Tools::getValue('extra_time_preparation'));
				Configuration::updateValue('DOD_PREPARATION_SATURDAY', (int)Tools::getValue('preparation_day_preparation_saturday'));
				Configuration::updateValue('DOD_PREPARATION_SUNDAY', (int)Tools::getValue('preparation_day_preparation_sunday'));
				Configuration::updateValue('DOD_DATE_FORMAT', Tools::getValue('date_format'));
				$this->_html .= $this->displayConfirmation($this->l('Settings are updated'));
			}
			else
				$this->_html .= $this->displayError(implode('<br />', $errors));
		}
		
		if (Tools::isSubmit('submitCarrierRule'))
		{	
			if (!Validate::isUnsignedInt(Tools::getValue('minimal_time')))
				$errors[] = $this->l('Minimum time is invalid');
			if (!Validate::isUnsignedInt(Tools::getValue('maximal_time')))
				$errors[] = $this->l('Maximum time is invalid');
			if (($carrier = new Carrier((int)Tools::getValue('id_carrier'))) AND !Validate::isLoadedObject($carrier))
				$errors[] = $this->l('Carrier is invalid');
			if ($this->_isAlreadyDefinedForCarrier((int)($carrier->id), (int)(Tools::getValue('id_carrier_rule', 0))))
				$errors[] = $this->l('You cannot use this carrier, a rule has already been saved.');
			
			if (!sizeof($errors))
			{
				if (Tools::isSubmit('addCarrierRule'))
				{
					if (Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'dateofdelivery_carrier_rule`(`id_carrier`, `minimal_time`, `maximal_time`, `delivery_saturday`, `delivery_sunday`) 
					VALUES ('.(int)($carrier->id).', '.(int)Tools::getValue('minimal_time').', '.(int)Tools::getValue('maximal_time').', '.(int)Tools::isSubmit('preparation_day_delivery_saturday').', '.(int)Tools::isSubmit('preparation_day_delivery_sunday').')
					'))
						Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&confirmAddCarrierRule');
					else
						$this->_html .= $this->displayError($this->l('An error occurred on adding of carrier rule.'));
				}
				else
				{
					if (Db::getInstance()->execute('
					UPDATE `'._DB_PREFIX_.'dateofdelivery_carrier_rule`  
					SET `id_carrier` = '.(int)($carrier->id).', `minimal_time` = '.(int)Tools::getValue('minimal_time').', `maximal_time` = '.(int)Tools::getValue('maximal_time').', `delivery_saturday` = '.(int)Tools::isSubmit('preparation_day_delivery_saturday').', `delivery_sunday` = '.(int)Tools::isSubmit('preparation_day_delivery_sunday').'
					WHERE `id_carrier_rule` = '.(int)Tools::getValue('id_carrier_rule')
					))
						Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&confirmupdatedateofdelivery');
					else
						$this->_html .= $this->displayError($this->l('An error occurred on updating of carrier rule.'));
				}
				
			}
			else
				$this->_html .= $this->displayError(implode('<br />', $errors));
		}
		
		if (Tools::isSubmit('deletedateofdelivery') AND Tools::isSubmit('id_carrier_rule') AND (int)Tools::getValue('id_carrier_rule') AND $this->_isCarrierRuleExists((int)Tools::getValue('id_carrier_rule')))
		{
			$this->_deleteByIdCarrierRule((int)Tools::getValue('id_carrier_rule'));
			$this->_html .= $this->displayConfirmation($this->l('Carrier rule deleted successfully'));
		}
		
		if (Tools::isSubmit('confirmAddCarrierRule'))
			$this->_html = $this->displayConfirmation($this->l('Carrier rule added successfully'));
		
		if (Tools::isSubmit('confirmupdatedateofdelivery'))
			$this->_html = $this->displayConfirmation($this->l('Carrier rule updated successfully'));
	}
	
	private function _getCarrierRulesWithCarrierName()
	{
		return Db::getInstance()->executeS('
		SELECT * 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` dcr 
		LEFT JOIN `'._DB_PREFIX_.'carrier` c ON (c.`id_carrier` = dcr.`id_carrier`)
		');
	}
	
	private function _getCarrierRule($id_carrier_rule)
	{
		if (!(int)($id_carrier_rule))
			return false;
		return Db::getInstance()->getRow('
		SELECT * 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier_rule` = '.(int)($id_carrier_rule)
		);
	}
	
	private function _getCarrierRuleWithIdCarrier($id_carrier)
	{
		if (!(int)($id_carrier))
			return false;
		return Db::getInstance()->getRow('
		SELECT * 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier` = '.(int)($id_carrier)
		);
	}
	
	private function _isCarrierRuleExists($id_carrier_rule)
	{
		if (!(int)($id_carrier_rule))
			return false;
		return (bool)Db::getInstance()->getValue('
		SELECT COUNT(*) 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier_rule` = '.(int)($id_carrier_rule)
		);
	}
	
	private function _deleteByIdCarrierRule($id_carrier_rule)
	{
		if (!(int)($id_carrier_rule))
			return false;
		return Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier_rule` = '.(int)($id_carrier_rule)
		);
	}
	
	private function _isAlreadyDefinedForCarrier($id_carrier, $id_carrier_rule = 0)
	{
		if (!(int)($id_carrier))
			return false;
		return (bool)Db::getInstance()->getValue('
		SELECT COUNT(*) 
		FROM `'._DB_PREFIX_.'dateofdelivery_carrier_rule` 
		WHERE `id_carrier` = '.(int)($id_carrier).'
		'.((int)$id_carrier_rule != 0 ? 'AND `id_carrier_rule` != '.(int)($id_carrier_rule) : ''));
	}

	/**
	 * @param int    $id_carrier
	 * @param bool   $product_oos
	 * @param string $date
	 *
	 * @return array|bool returns the min & max delivery date
	 */
	private function _getDatesOfDelivery($id_carrier, $product_oos = false, $date = null)
	{
		if (!(int)($id_carrier))
			return false;
		$carrier_rule = $this->_getCarrierRuleWithIdCarrier((int)($id_carrier));
		if (empty($carrier_rule))
			return false;

		if ($date != null AND Validate::isDate($date))
			$date_now = strtotime($date);
		else
			$date_now = time(); // Date on timestamp format
		if ($product_oos)
			$date_now += Configuration::get('DOD_EXTRA_TIME_PRODUCT_OOS') * 24 * 3600;
		if (!Configuration::get('DOD_PREPARATION_SATURDAY') AND date('l', $date_now) == 'Saturday')
			$date_now += 24 * 3600;
		if (!Configuration::get('DOD_PREPARATION_SUNDAY') AND date('l', $date_now) == 'Sunday')
			$date_now += 24 * 3600;

		$date_minimal_time = $date_now + ($carrier_rule['minimal_time'] * 24 * 3600) + (Configuration::get('DOD_EXTRA_TIME_PREPARATION') * 24 * 3600);
		$date_maximal_time = $date_now + ($carrier_rule['maximal_time'] * 24 * 3600) + (Configuration::get('DOD_EXTRA_TIME_PREPARATION') * 24 * 3600);
		
		if (!$carrier_rule['delivery_saturday'] AND date('l', $date_minimal_time) == 'Saturday')
		{
			$date_minimal_time += 24 * 3600;
			$date_maximal_time += 24 * 3600;
		}
		if (!$carrier_rule['delivery_saturday'] AND date('l', $date_maximal_time) == 'Saturday')
			$date_maximal_time += 24 * 3600;
		
		if (!$carrier_rule['delivery_sunday'] AND date('l', $date_minimal_time) == 'Sunday')
		{
			$date_minimal_time += 24 * 3600;
			$date_maximal_time += 24 * 3600;
		}
		if (!$carrier_rule['delivery_sunday'] AND date('l', $date_maximal_time) == 'Sunday')
			$date_maximal_time += 24 * 3600;

		/*
		
		// Do not remove this commentary, it's usefull to allow translations of months and days in the translator tool
		
		$this->l('Sunday');
		$this->l('Monday');
		$this->l('Tuesday');
		$this->l('Wednesday');
		$this->l('Thursday');
		$this->l('Friday');
		$this->l('Saturday');

		$this->l('January');
		$this->l('February');
		$this->l('March');
		$this->l('April');
		$this->l('May');
		$this->l('June');
		$this->l('July');
		$this->l('August');
		$this->l('September');
		$this->l('October');
		$this->l('November');
		$this->l('December');
		*/
		
		$date_minimal_string = '';
		$date_maximal_string = '';
		$date_format = preg_split('/([a-z])/Ui', Configuration::get('DOD_DATE_FORMAT'), NULL, PREG_SPLIT_DELIM_CAPTURE);
		foreach ($date_format as $elmt)
		{
			if ($elmt == 'l' OR $elmt == 'F')
			{
				$date_minimal_string .= $this->l(date($elmt, $date_minimal_time));
				$date_maximal_string .= $this->l(date($elmt, $date_maximal_time));
			}
			elseif (preg_match('/[a-z]/Ui', $elmt))
			{
				$date_minimal_string .= date($elmt, $date_minimal_time);
				$date_maximal_string .= date($elmt, $date_maximal_time);
			}
			else
			{
				$date_minimal_string .= $elmt;
				$date_maximal_string .= $elmt;
			}
		}
		return array(
			array(
				$date_minimal_string,
				$date_minimal_time
			),
			array(
			$date_maximal_string,
				$date_maximal_time
			)
		);
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Extra time when a product is out of stock'),
						'name' => 'extra_time_product_oos',
						'suffix' => $this->l('day(s)'),
						),
					array(
						'type' => 'text',
						'label' => $this->l('Extra time for preparation of the order'),
						'name' => 'extra_time_preparation',
						'suffix' => $this->l('day(s)'),
						),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Preparation option'),
						'name' => 'preparation_day',
						'values' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => array(
								array(
									'id' => 'preparation_saturday',
									'name' => $this->l('Saturday preparation'),
									'val' => 1
								),
								array(
									'id' => 'preparation_sunday',
									'name' => $this->l('Sunday preparation'),
									'val' => 1
								),
							),
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('Date format:'),
						'name' => 'date_format',
						'desc' => $this->l('You can see all parameters available at:').' <a href="http://www.php.net/manual/en/function.date.php">http://www.php.net/manual/en/function.date.php</a>',
						),
				),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default')
			),
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitMoreOptions';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function renderAddForm()
	{
		$carriers = Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS);

		foreach ($carriers as $key => $val)
			$carriers[$key]['name'] = (!$val['name'] ? Configuration::get('PS_SHOP_NAME') : $val['name']);
		
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
						array(
						'type' => 'select',
						'label' => $this->l('Carrier :'),
						'name' => 'id_carrier',
						'options' => array(
							'query' => $carriers,
							'id' => 'id_carrier',
							'name' => 'name'
						)
					),
					array(
						'type' => 'text',
						'label' => $this->l('Delivery between'),
						'name' => 'minimal_time',
						'suffix' => $this->l('day(s)'),
						),
					array(
						'type' => 'text',
						'label' => $this->l(''),
						'name' => 'maximal_time',
						'suffix' => $this->l('day(s)'),
						),
					array(
						'type' => 'checkbox',
						'label' => $this->l('Preparation option'),
						'name' => 'preparation_day',
						'values' => array(
							'id' => 'id',
							'name' => 'name',
							'query' => array(
								array(
									'id' => 'delivery_saturday',
									'name' => $this->l('Saturday preparation'),
									'val' => 1
								),
								array(
									'id' => 'delivery_sunday',
									'name' => $this->l('Sunday preparation'),
									'val' => 1
								),
							),
						)
					)
				),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default',
				'name' => 'submitCarrierRule',
				)
			),
		);
		
		if (Tools::getValue('id_carrier_rule') && $this->_isCarrierRuleExists(Tools::getValue('id_carrier_rule')))
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_carrier_rule');
		
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		
		if (Tools::getValue('id_carrier_rule'))
			$helper->submit_action = 'updatedateofdelivery';
		else
			$helper->submit_action = 'addCarrierRule';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getCarrierRuleFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}
	
	public function getConfigFieldsValues()
	{
		return array(
			'extra_time_product_oos' => Tools::getValue('extra_time_product_oos', Configuration::get('DOD_EXTRA_TIME_PRODUCT_OOS')),
			'extra_time_preparation' => Tools::getValue('extra_time_preparation', Configuration::get('DOD_EXTRA_TIME_PREPARATION')),
			'preparation_day_preparation_saturday' => Tools::getValue('preparation_day_preparation_saturday', Configuration::get('DOD_PREPARATION_SATURDAY')),
			'preparation_day_preparation_sunday' => Tools::getValue('preparation_day_preparation_sunday', Configuration::get('DOD_PREPARATION_SUNDAY')),
			'date_format' => Tools::getValue('date_format', Configuration::get('DOD_DATE_FORMAT')),
			'id_carrier' => Tools::getValue('id_carrier'),
		);
	}
	
	public function getCarrierRuleFieldsValues()
	{
		$fields = array(
			'id_carrier_rule' => Tools::getValue('id_carrier_rule'),
			'id_carrier' => Tools::getValue('id_carrier'),
			'minimal_time' => Tools::getValue('minimal_time'),
			'maximal_time' => Tools::getValue('maximal_time'),
			'delivery_saturday' => Tools::getValue('delivery_saturday'),
			'delivery_sunday' => Tools::getValue('delivery_sunday'),
			);
		
		if (Tools::isSubmit('updatedateofdelivery') AND $this->_isCarrierRuleExists(Tools::getValue('id_carrier_rule')))
		{
			$carrier_rule = $this->_getCarrierRule(Tools::getValue('id_carrier_rule'));

			$fields['id_carrier_rule'] = Tools::getValue('id_carrier_rule', $carrier_rule['id_carrier_rule']);
			$fields['id_carrier'] = Tools::getValue('id_carrier', $carrier_rule['id_carrier']);
			$fields['minimal_time'] = Tools::getValue('minimal_time', $carrier_rule['minimal_time']);
			$fields['maximal_time'] = Tools::getValue('maximal_time', $carrier_rule['maximal_time']);
			$fields['preparation_day_delivery_saturday'] = Tools::getValue('preparation_day_delivery_saturday', $carrier_rule['delivery_saturday']);
			$fields['preparation_day_delivery_sunday'] = Tools::getValue('preparation_day_delivery_sunday', $carrier_rule['delivery_sunday']);
		}

		return $fields;
	}
	
	public function renderList()
	{
		
		$add_url = $this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&addCarrierRule=1';
		
		$fields_list = array(
			'name' => array(
				'title' => $this->l('Name of carrier'),
				'type' => 'text',
			),
			'delivery_between' => array(
				'title' => $this->l('Delivery between'),
				'type' => 'text',
			),
			'delivery_saturday' => array(
				'title' => $this->l('Saturday delivery'),
				'type' => 'bool',
				'align' => 'center',
				'active' => 'status',
			),
			'delivery_sunday' => array(
				'title' => $this->l('Sunday delivery'),
				'type' => 'bool',
				'align' => 'center',
				'active' => 'status',
			),
		);
		$list = $this->_getCarrierRulesWithCarrierName();
		
		foreach ($list as $key => $val)
		{
			if (!$val['name'])
				$list[$key]['name'] = Configuration::get('PS_SHOP_NAME');
			$list[$key]['delivery_between'] = sprintf($this->l('%1$d day(s) and %2$d day(s)'), $val['minimal_time'], $val['maximal_time']);
		}
		
		$helper = new HelperList();
		$helper->shopLinkType = '';
		$helper->simple_header = true;
		$helper->identifier = 'id_carrier_rule';
		$helper->actions = array('edit', 'delete');
		$helper->show_toolbar = false;

		$helper->title = $this->l('Link list');
		$helper->table = $this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		
		$this->context->smarty->assign(array('add_url' => $add_url));
		
		return $this->display(__FILE__, 'button.tpl').$helper->generateList($list, $fields_list).$this->display(__FILE__, 'button.tpl');
	}
}